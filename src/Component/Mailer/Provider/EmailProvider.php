<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Provider;


use App\Component\Mailer\Emails;
use App\Component\Mailer\Model\Email;
use App\Component\Mailer\Model\EmailInterface;
use InvalidArgumentException;

final class EmailProvider implements EmailProviderInterface
{
    /**
     * @var array
     */
    private array $configuration = [
        Emails::USER_CONFIRMATION => [
            'subject' => 'Подтверждение почтового адреса',
            'template' => 'Email/userConfirmation.html.twig'
        ],
        Emails::USER_PASSWORD_RESET => [
            'subject' => 'Восстановление пароля',
            'template' => 'Email/userPasswordReset.html.twig'
        ],
        Emails::TEST => [
            'subject' => 'Тестовое письмо',
            'template' => 'Email/test.html.twig'
        ]
    ];

    /**
     * @param string $code
     * @param string|null $subject
     * @return EmailInterface
     */
    public function getEmail(string $code, ?string $subject = null): EmailInterface
    {
        if (!isset($this->configuration[$code])) {
            throw new InvalidArgumentException(sprintf('Email with code "%s" does not exist!', $code));
        }

        $configuration = $this->configuration[$code];
        $email = new Email($code);
        $email->setTemplate($configuration['template']);
        $email->setSubject($subject ?? $configuration['subject']);

        return $email;
    }
}