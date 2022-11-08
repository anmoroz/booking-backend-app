<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Event;

use App\Entity\UserCode;
use Symfony\Contracts\EventDispatcher\Event;

class EmailConfirmationEvent extends Event
{
    public function __construct(private UserCode $userCode)
    {
    }

    /**
     * @return UserCode
     */
    public function getUserCode(): UserCode
    {
        return $this->userCode;
    }
}
