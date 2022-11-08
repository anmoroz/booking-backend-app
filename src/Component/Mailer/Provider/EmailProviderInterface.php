<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Provider;


use App\Component\Mailer\Model\EmailInterface;

interface EmailProviderInterface
{
    public function getEmail(string $code, ?string $subject = null): EmailInterface;
}