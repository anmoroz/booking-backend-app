<?php

declare(strict_types=1);


namespace App\Helper;

use Exception;

abstract class RandomStringGenerator
{
    /**
     * @param int $lenght
     * @return string
     */
    public static function generate(int $lenght): string
    {
        try {

            return strtr(substr(base64_encode(random_bytes($lenght)), 0, $lenght), '+/', 'ps');
        } catch (Exception) {

            return uniqid();
        }
    }
}