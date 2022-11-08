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
use App\Repository\UserCodeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SignUpService
{
    private const NEXT_SEND_VERIFICATION_CODE_IN_TIME = 60;

    public function __construct(
        private UserRepository $userRepository,
        private UserCodeRepository $userCodeRepository,
        private UserCodeService $userCodeService,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    /**
     * @param EmailDTO $emailDTO
     * @return TextMessage
     */
    public function sendConfirmationEmail(EmailDTO $emailDTO): TextMessage
    {
        $email = $emailDTO->getEmail();

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user
                ->setEmail($email)
                ->setPassword(RandomStringGenerator::generate(10))
                ->setIsVerified(false)
                ->setName('')
                ->setRoles([User::ROLE_USER]);
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