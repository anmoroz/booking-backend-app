<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Renderer\Adapter;


use App\Component\Mailer\MailerException;
use App\Component\Mailer\Model\EmailInterface;
use App\Component\Mailer\Renderer\RenderedEmail;
use Throwable;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

class EmailTwigAdapter implements AdapterInterface
{
    public function __construct(private Environment $twig)
    {
    }


    /**
     * @param EmailInterface $email
     * @param array $data
     * @return RenderedEmail
     * @throws MailerException
     */
    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        if (null !== $email->getTemplate()) {

            return $this->provideEmailWithTemplate($email, $data);
        }

        return $this->provideEmailWithoutTemplate($email, $data);
    }

    /**
     * @param EmailInterface $email
     * @param array $data
     * @return RenderedEmail
     * @throws MailerException
     */
    private function provideEmailWithTemplate(EmailInterface $email, array $data): RenderedEmail
    {
        try {
            $data = $this->twig->mergeGlobals($data);
            $templateWrapper = $this->twig->load((string) $email->getTemplate());
            $subject = $templateWrapper->renderBlock('subject', $data);
            $body = $templateWrapper->renderBlock('body', $data);
        } catch (Throwable $e) {
            throw new MailerException($e->getMessage());
        }

        return new RenderedEmail($subject, $body);
    }

    /**
     * @param EmailInterface $email
     * @param array $data
     * @return RenderedEmail
     * @throws MailerException
     */
    private function provideEmailWithoutTemplate(EmailInterface $email, array $data): RenderedEmail
    {
        $this->twig->setLoader(new ArrayLoader());
        try {
            $subjectTemplate = $this->twig->createTemplate((string) $email->getSubject());
            $bodyTemplate = $this->twig->createTemplate((string) $email->getContent());

            $subject = $subjectTemplate->render($data);
            $body = $bodyTemplate->render($data);
        } catch (Throwable $e) {
            throw new MailerException($e->getMessage());
        }

        return new RenderedEmail($subject, $body);
    }
}