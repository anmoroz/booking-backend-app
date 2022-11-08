<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class EmailDTO
{
    #[Assert\NotBlank(message: "Введите Email")]
    #[Assert\Email(message: "Поле должно содержать валидное значение электронного адреса")]
    private ?string $email = null;

    /**
     * @param string|null $email
     */
    public function __construct(?string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}