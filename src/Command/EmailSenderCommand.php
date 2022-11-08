<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Command;

use App\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Component\Mailer\Emails;

#[AsCommand(
    name: 'app:email:send-test',
    description: 'Отправка тестового Email',
)]
class EmailSenderCommand extends Command
{
    public function __construct(private SenderInterface $emailSender)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Email назначения');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $to = $input->getOption('to');
        if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            $outputStyle->error('Невалидный email');

            return Command::FAILURE;
        }

        ob_start();
        phpinfo(INFO_MODULES);
        $phpinfo = ob_get_contents();
        ob_end_clean();


        $this->emailSender->send(Emails::TEST, $to, ['phpinfo' => nl2br($phpinfo)]);

        return Command::SUCCESS;
    }
}