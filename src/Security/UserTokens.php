<?php

declare(strict_types=1);


namespace App\Security;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class UserTokens
{
    public const TOKEN_TYPE = 'Bearer';

    /**
     * @Groups({"show"})
     */
    private string $tokenType = self::TOKEN_TYPE;

    /**
     * @Groups({"show"})
     */
    private string $accessToken;


    /**
     * @Groups({"show"})
     */
    private string $refreshToken;

    /**
     * @Groups({"show"})
     */
    private DateTimeInterface $refreshTokenExpiresAt;

    /**
     * @param string $accessToken
     * @param string $refreshToken
     * @param DateTimeInterface $refreshTokenExpiresAt
     */
    public function __construct(string $accessToken, string $refreshToken, DateTimeInterface $refreshTokenExpiresAt)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->refreshTokenExpiresAt = $refreshTokenExpiresAt;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return DateTimeInterface
     */
    public function getRefreshTokenExpiresAt(): DateTimeInterface
    {
        return $this->refreshTokenExpiresAt;
    }
}