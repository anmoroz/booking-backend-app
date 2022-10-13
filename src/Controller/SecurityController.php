<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Controller;

use App\Model\RefreshTokenDTO;
use App\Security\UserCredentials;
use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/security', name: 'security_')]
class SecurityController extends AbstractController
{
    public function __construct(private SecurityService $securityService)
    {
    }

    #[Route('/authenticate', methods: ['POST'], name: 'authenticate')]
    public function authenticate(UserCredentials $credentials): JsonResponse
    {
        try {
            $userTokens = $this->securityService->authenticate($credentials);
        } catch (Exception) {
            throw new BadRequestHttpException('Неверные учетные данные');
        }

        return $this->json($userTokens, Response::HTTP_OK, [], ['groups' => 'show']);
    }

    #[Route('/refresh-token', methods: ['POST'], name: 'refresh-token')]
    public function refreshToken(RefreshTokenDTO $refreshTokenDTO): JsonResponse
    {
        try {
            $userTokens = $this->securityService->refreshAccessToken($refreshTokenDTO->getRefreshToken());
        } catch (Exception) {
            throw new BadRequestHttpException('Требуется авторизация');
        }

        return $this->json($userTokens, Response::HTTP_OK, [], ['groups' => 'show']);
    }
}
