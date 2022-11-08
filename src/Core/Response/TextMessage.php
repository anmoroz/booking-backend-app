<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class TextMessage
{
    #[Groups(["show"])]
    public string $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }
}