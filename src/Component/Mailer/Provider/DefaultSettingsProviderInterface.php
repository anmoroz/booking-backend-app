<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Provider;


interface DefaultSettingsProviderInterface
{
    public function getSenderName(): string;

    public function getSenderAddress(): string;
}