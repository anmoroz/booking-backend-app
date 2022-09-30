<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class RoomDetails
{
    #[Assert\NotBlank(message: "Введите название объекта")]
    private string $name;


    #[Assert\NotBlank(message: "Введите адрес объекта")]
    private string $address;

    /**
     * @param string $name
     * @param string $address
     */
    public function __construct(string $name, string $address)
    {
        $this->name = $name;
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }
}