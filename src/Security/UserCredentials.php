<?php

declare(strict_types=1);


namespace App\Security;

use Symfony\Component\Validator\Constraints as Assert;

class UserCredentials
{
    /**
     * @Assert\NotBlank(message="Поле не должно иметь пустое значение")
     * @Assert\Email(message="Поле должно содержать валидное значение электронного адреса")
     */
    private string $email;

    /**
     * @Assert\NotBlank(message="Поле не должно иметь пустое значение")
     */
    private string $password;

    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}