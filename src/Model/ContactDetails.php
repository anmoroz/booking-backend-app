<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDetails
{
    #[Assert\Sequentially([
        new Assert\NotBlank(message: "Введите телефон гостя"),
        new Assert\Regex('/^\d{10}$/', message: 'Номер телефона должен состоять из 10 цифр')
    ])]
    private ?string $phone = null;

    #[Assert\NotBlank(message: "Введите имя гостя")]
    private ?string $name = null;

    private string $note = '';

    /**
     * @param string|null $phone
     * @param string|null $name
     * @param string $note
     */
    public function __construct(?string $phone, ?string $name, string $note = '')
    {
        $this->phone = $phone;
        $this->name = $name;
        $this->note = $note;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }
}