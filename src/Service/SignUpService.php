<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Component\Mailer\MailerException;
use App\Core\Helper\RandomStringGenerator;
use App\Core\Response\TextMessage;
use App\Entity\User;
use App\Entity\UserCode;
use App\Event\EmailConfirmationEvent;
use App\Model\EmailDTO;
use App\Model\Security\ConfirmDataDTO;
use App\Repository\UserCodeRepository;
use App\Repository\UserRepository;
use App\Security\JwtTokenProvider;
use App\Security\UserTokens;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SignUpService
{
    private const NEXT_SEND_VERIFICATION_CODE_IN_TIME = 60;

    public function __construct(
        private UserRepository $userRepository,
        private UserCodeRepository $userCodeRepository,
        private UserCodeService $userCodeService,
        private EventDispatcherInterface $eventDispatcher,
        private SecurityService $securityService,
        private JwtTokenProvider $jwtTokenProvider,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    /**
     * @param ConfirmDataDTO $confirmDataDTO
     * @return UserTokens
     */
    public function confirm(ConfirmDataDTO $confirmDataDTO): UserTokens
    {
        /** @var UserBadge $userBadge */
        try {
            $userBadge  = $this->jwtTokenProvider->decode($confirmDataDTO->getToken());
        } catch (UserNotFoundException) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        $user = $this->userRepository->findByEmail($userBadge->getUserIdentifier());
        if ($user->isRegistrationCompleted()) {
            throw new BadRequestHttpException('Пользователь уже зарегистрирован. Пожалуйста авторизуйтесь.');
        }

        $user
            ->setPassword($this->passwordHasher->hashPassword($user, $confirmDataDTO->getPassword()))
            ->setName($confirmDataDTO->getName())
            ->setIsRegistrationCompleted(true)
            ->setCredentialsUpdatedAtNow();

        $this->userRepository->add($user, true);

        return $this->securityService->createUserTokens($user);
    }

    /**
     * @param UserCode $userCode
     * @return array
     */
    public function verify(UserCode $userCode): array
    {
        $user = $userCode->getUser();

        $user->removeUserCode($userCode);
        $user->setIsVerified(true);

        $this->userRepository->add($user, true);

        if ($user->isRegistrationCompleted()) {
            $userTokens = $this->securityService->authenticateByCode($user);

            return [
                'message' => 'Пользователь уже зарегистрирован',
                'accessToken' => $userTokens->getAccessToken()
            ];
        }

        return [
            'message' => 'Email подтвержден',
            'token' => $this->jwtTokenProvider->generate($user)
        ];
    }

    /**
     * @param EmailDTO $emailDTO
     * @return TextMessage
     */
    public function sendConfirmationEmail(EmailDTO $emailDTO): TextMessage
    {
        $email = $emailDTO->getEmail();

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user && $user->isRegistrationCompleted()) {
            throw new BadRequestHttpException('Такой Email уже зарегистрирован. Пожалуйста, авторизуйтесь.');
        }

        if (!$user) {
            $user = new User();
            $user
                ->setEmail($email)
                ->setPassword(RandomStringGenerator::generate(10))
                ->setIsVerified(false)
                ->setName('')
                ->setRoles([User::ROLE_USER])
                ->setCredentialsUpdatedAtNow();
        } else {
            $this->checkValidationCodeLeftTime($user);

            foreach ($user->getUserCodes() as $userCode) {
                $this->userCodeRepository->remove($userCode, true);
            }
        }

        $code = $this->userCodeService->createEmailVerificationCode();
        $user->addUserCode($code);

        $this->userRepository->add($user, true);

        $emailConfirmationEvent = new EmailConfirmationEvent($code);
        try {
            $this->eventDispatcher->dispatch($emailConfirmationEvent);
        } catch (MailerException) {
            throw new BadRequestHttpException('Почтовый сервис временно не работает');
        }

        return new TextMessage(sprintf('Код отправлен в письме на адрес %s', $user->getEmail()));
    }

    /**
     * @param User $user
     * @throws BadRequestHttpException
     * @return void
     */
    private function checkValidationCodeLeftTime(User $user): void
    {
        $emailVerificationCode = $this->userCodeRepository->findByUserAndType(
            $user,
            UserCode::TYPE_EMAIL_VERIFICATION
        );
        if ($emailVerificationCode) {
            $diffSeconds = time() - $emailVerificationCode->getCreatedAt()->getTimestamp();
            if ($diffSeconds < self::NEXT_SEND_VERIFICATION_CODE_IN_TIME) {
                throw new BadRequestHttpException('Повторная отправка кода возможна через 1 минуту');
            }
        }
    }
}