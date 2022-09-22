<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Exception;

use Exception;

class UserIsNotAuthenticateException extends Exception
{
    public function __construct()
    {
        parent::__construct('The user is not authenticated');
    }
}