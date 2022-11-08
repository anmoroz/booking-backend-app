<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Sender;


use App\Component\Mailer\MailerException;
use App\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use App\Component\Mailer\Provider\EmailProviderInterface;
use App\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Sender implements SenderInterface
{
    /**
     * @param RendererAdapterInterface $rendererAdapter
     * @param EmailProviderInterface $provider
     * @param DefaultSettingsProviderInterface $defaultSettingsProvider
     * @param MailerInterface $mailer
     */
    public function __construct(
        private RendererAdapterInterface $rendererAdapter,
        private EmailProviderInterface $provider,
        private DefaultSettingsProviderInterface $defaultSettingsProvider,
        private MailerInterface $mailer,
    ) {

    }

    /**
     * @param string $code
     * @param string $recipient
     * @param array $data
     * @param array $attachments
     * @param string|null $replyTo
     * @return void
     */
    public function send(
        string $code,
        string $recipient,
        array $data = [],
        array $attachments = [],
        ?string $replyTo = null
    ): void
    {
        $email = $this->provider->getEmail($code, $data['subject'] ?? null);

        $senderAddress = $email->getSenderAddress() ?: $this->defaultSettingsProvider->getSenderAddress();
        $senderName = $email->getSenderName() ?: $this->defaultSettingsProvider->getSenderName();

        $renderedEmail = $this->rendererAdapter->render($email, $data);

        $email = (new Email())
            ->subject($renderedEmail->getSubject())
            ->from(new Address($senderAddress, $senderName))
            ->to($recipient)
            ->html($renderedEmail->getBody());

        foreach ($attachments as $attachment) {
            $email->attachFromPath($attachment);
        }

        if ($replyTo) {
            $email->replyTo($replyTo);
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new MailerException($e->getMessage());
        }
    }
}