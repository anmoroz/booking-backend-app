<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Service;


use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use App\Security\JwtTokenProvider;
use App\Security\UserCredentials;
use App\Security\UserTokens;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityService
{

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private RefreshTokenRepository $refreshTokenRepository,
        private JwtTokenProvider $jwtTokenProvider
    )
    {
    }

    /**
     * @param UserCredentials $credentials
     * @return UserTokens
     * @throws Exception
     */
    public function authenticate(UserCredentials $credentials): UserTokens
    {
        $user = $this->userRepository->findByEmail($credentials->getEmail());


        if (!$user || false === $this->passwordHasher->isPasswordValid($user, $credentials->getPassword())) {
            throw new Exception();
        }

        return $this->createUserTokens($user);
    }

    /**
     * @param User $user
     * @return UserTokens
     */
    private function createUserTokens(User $user): UserTokens
    {
        $accessToken = $this->jwtTokenProvider->generate($user);
        $refreshToken = $this->refreshTokenRepository->createByUser($user);

        return new UserTokens($accessToken, $refreshToken->getToken(), $refreshToken->getExpiresAt());
    }
}