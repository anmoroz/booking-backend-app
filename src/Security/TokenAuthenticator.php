<?php

declare(strict_types=1);


namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    private const HEADER_AUTH_TOKEN = 'Authorization';
    private const TOKEN_PREFIX = 'Bearer ';

    /**
     * @param JwtTokenProvider $jwtTokenProvider
     */
    public function __construct(private JwtTokenProvider $jwtTokenProvider)
    {
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        if (!$request->headers->has(self::HEADER_AUTH_TOKEN)) {

            return false;
        }

        $authorizationValue = trim((string) $request->headers->get(self::HEADER_AUTH_TOKEN));

        if (
            strlen($authorizationValue) === 0
            && substr($authorizationValue, 0, strlen(self::TOKEN_PREFIX)) !== self::TOKEN_PREFIX
        ) {

            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $authorizationValue = trim($request->headers->get(self::HEADER_AUTH_TOKEN));
        $apiToken = substr($authorizationValue, strlen(self::TOKEN_PREFIX));

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('Auth token not found (header: "{{ header }}")', [
                '{{ header }}' => self::HEADER_AUTH_TOKEN,
            ]);
        }

        $userBadge = $this->jwtTokenProvider->decode($apiToken);

        if (is_null($userBadge)) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        return new SelfValidatingPassport($userBadge);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }
}