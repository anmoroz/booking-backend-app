<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Component\Mailer\Emails;
use App\Component\Mailer\Sender\SenderInterface;

class MailerService
{
    public function __construct(private SenderInterface $emailSender)
    {
    }

    /**
     * @param string $recipient
     * @param array $data
     * @return void
     */
    public function sendUserConfirmationEmail(string $recipient, array $data): void
    {
        $this->emailSender->send(Emails::USER_CONFIRMATION, $recipient, $data);
    }
}