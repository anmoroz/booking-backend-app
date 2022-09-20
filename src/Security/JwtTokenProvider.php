<?php
declare(strict_types=1);


namespace App\Security;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JwtTokenProvider
{
    private const ALG = 'HS256';

    private string $secretKey;

    private int $validTime;

    /**
     * JwtTokenProvider constructor.
     * @param string $secretKey
     * @param int $validTime
     */
    public function __construct(string $secretKey, int $validTime)
    {
        $this->secretKey = $secretKey;
        $this->validTime = $validTime;
    }

    /**
     * @param User $user
     * @return string
     */
    public function generate(User $user): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->validTime;

        $payload = [
            'email' => $user->getEmail(),
            'updatedAt' => $user->getCredentialsUpdatedAt()->getTimestamp(),
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];

        return JWT::encode($payload, $this->secretKey, self::ALG);
    }

    /**
     * @param string $token
     * @return UserBadge|null
     */
    public function decode(string $token): ?UserBadge
    {
        try {
            $payload = (array) JWT::decode($token, new Key($this->secretKey, self::ALG));

            return new UserBadge($payload['email']);
        } catch (Exception) {
            return null;
        }
    }
}