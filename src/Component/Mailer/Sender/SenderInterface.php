<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Sender;


interface SenderInterface
{
    public function send(
        string $code,
        string $recipient,
        array $data = [],
        array $attachments = [],
        ?string $replyTo = null
    ): void;
}