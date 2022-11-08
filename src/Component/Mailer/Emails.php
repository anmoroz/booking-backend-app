<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer;


interface Emails
{
    public const USER_CONFIRMATION = 'user_confirmation';

    public const USER_PASSWORD_RESET = 'user_password_reset';

    public const TEST = 'test';
}