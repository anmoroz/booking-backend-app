<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Model;


interface EmailInterface
{
    public function getCode(): string;

    public function getSubject(): ?string;

    public function setSubject(string $subject): void;

    public function getContent(): ?string;

    public function setContent(string $content): void;

    public function getTemplate(): ?string;

    public function setTemplate(string $template): void;

    public function getSenderName(): ?string;

    public function setSenderName(string $senderName): void;

    public function getSenderAddress(): ?string;

    public function setSenderAddress(string $senderAddress): void;
}