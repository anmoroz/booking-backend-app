<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\EventSubscriber;

use App\Event\EmailConfirmationEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private MailerService $mailerService)
    {
    }

    public function onEmailConfirmation(EmailConfirmationEvent $confirmationEvent): void
    {
        $user = $confirmationEvent->getUserCode()->getUser();
        $code = $confirmationEvent->getUserCode()->getCode();
        $this->mailerService->sendUserConfirmationEmail(
            $user->getEmail(),
            ['user' => $user, 'code' => $code]
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailConfirmationEvent::class => 'onEmailConfirmation',
        ];
    }
}