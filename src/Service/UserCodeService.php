<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Entity\UserCode;

class UserCodeService
{
    /**
     * @return UserCode
     */
    public function createEmailVerificationCode(): UserCode
    {
        $code = new UserCode();
        $code->setType(UserCode::TYPE_EMAIL_VERIFICATION)
            ->setRandomNumberCode()
            ->setCreatedAtNow()
        ;

        return $code;
    }
}