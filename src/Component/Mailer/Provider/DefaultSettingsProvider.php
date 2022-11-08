<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Provider;


final class DefaultSettingsProvider implements DefaultSettingsProviderInterface
{
    private string $senderName;

    private string $senderAddress;

    /**
     * @param string $senderName
     * @param string $senderAddress
     */
    public function __construct(string $senderName, string $senderAddress)
    {
        $this->senderName = $senderName;
        $this->senderAddress = $senderAddress;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @return string
     */
    public function getSenderAddress(): string
    {
        return $this->senderAddress;
    }
}