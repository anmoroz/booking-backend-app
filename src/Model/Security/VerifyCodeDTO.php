<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Model\Security;

use App\Entity\UserCode;

class VerifyCodeDTO
{
    private UserCode $userCode;

    private string $email;

    /**
     * @param UserCode $userCode
     * @param string $email
     */
    public function __construct(UserCode $userCode, string $email)
    {
        $this->userCode = $userCode;
        $this->email = $email;
    }

    /**
     * @return UserCode
     */
    public function getUserCode(): UserCode
    {
        return $this->userCode;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}