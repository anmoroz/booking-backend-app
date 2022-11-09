<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Model\Security;

use Symfony\Component\Validator\Constraints as Assert;

class ConfirmDataDTO
{
    #[Assert\NotBlank(message: "Поле не должно иметь пустое значение")]
    private ?string $token = null;

    #[Assert\NotBlank(message: "Введите пароль")]
    #[Assert\Length(min: 8, minMessage: "Пароль должен содержать не менее 8-ти символов")]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Введите ваше имя")]
    #[Assert\Length(max: 120, maxMessage: "Имя не должно превышать 120-ти символов")]
    private ?string $name = null;

    /**
     * @param string|null $token
     * @param string|null $password
     * @param string|null $name
     */
    public function __construct(?string $token, ?string $password, ?string $name)
    {
        $this->token = $token;
        $this->password = $password;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}