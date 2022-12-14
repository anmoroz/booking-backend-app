<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Controller;

use App\Model\EmailDTO;
use App\Model\RefreshTokenDTO;
use App\Model\Security\ConfirmDataDTO;
use App\Model\Security\VerifyCodeDTO;
use App\Security\UserCredentials;
use App\Service\SecurityService;
use App\Service\SignUpService;
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
            throw new BadRequestHttpException('Invalid refreshToken');
        }

        return $this->json($userTokens, Response::HTTP_OK, [], ['groups' => 'show']);
    }

    #[Route('/sign-up', methods: ['POST'], name: 'sign-up')]
    public function signUp(EmailDTO $emailDTO, SignUpService $signUpService): JsonResponse
    {
        $responseData = $signUpService->sendConfirmationEmail($emailDTO);

        return $this->json($responseData, Response::HTTP_OK);
    }

    #[Route('/sign-up/verify', methods: ['POST'], name: 'verify')]
    public function verify(VerifyCodeDTO $verifyCodeDTO, SignUpService $signUpService): JsonResponse
    {
        $responseData = $signUpService->verify($verifyCodeDTO->getUserCode());

        return $this->json($responseData, Response::HTTP_OK);
    }

    #[Route('/sign-up/confirm', methods: ['POST'], name: 'confirm')]
    public function confirm(ConfirmDataDTO $confirmDataDTO, SignUpService $signUpService): JsonResponse
    {
        $userTokens = $signUpService->confirm($confirmDataDTO);

        return $this->json($userTokens, Response::HTTP_OK);
    }
}
