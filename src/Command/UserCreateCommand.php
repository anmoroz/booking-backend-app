<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user',
)]
class UserCreateCommand extends Command
{
    private SymfonyStyle $io;

    private QuestionHelper $questionHelper;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private UserRepository $userRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email address')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->questionHelper = $this->getHelper('question');

        $io = new SymfonyStyle($input, $output);

        // Email
        $email = $this->getEmail($input);
        if (!$email) {

            return Command::FAILURE;
        }

        // Password
        $password = $this->getPassword($input, $output);
        if (!$password) {

            return Command::FAILURE;
        }

        $question = new Question('Введите имя: ');
        $name = $this->questionHelper->ask($input, $output, $question);

        $question = new Question('Введите телефон: ');
        $phone = $this->questionHelper->ask($input, $output, $question);

        $user = new User();
        $user
            ->setName($name)
            ->setEmail($email)
            ->setPhone($phone)
            ->setPassword($this->passwordHasher->hashPassword($user, $password))
            ->setRoles([User::ROLE_USER])
            ->setCredentialsUpdatedAtNow();

        $this->userRepository->add($user, true);

        $io->success(sprintf('Пользователь %s успешно создан!', $email));

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return string|null
     */
    private function getEmail(InputInterface $input): ?string
    {
        $email = $input->getArgument('email');
        $errors = $this->validator->validate($email, new Email());
        if (count($errors) > 0) {
            $this->io->error(sprintf('%s некорректный email', $email));

            return null;
        }

        if ($this->userRepository->findByEmail($email)) {
            $this->io->error(sprintf('Пользователь с таким email "%s" уже существует', $email));

            return null;
        }

        return $email;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string|null
     */
    private function getPassword(InputInterface $input, OutputInterface $output): ?string
    {
        $question = new Question('Введите пароль: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $this->questionHelper->ask($input, $output, $question);

        if (!$password || !trim($password)) {
            $this->io->error('Пароль пустой');

            return null;
        }

        return $password;
    }
}
