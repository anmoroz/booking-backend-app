<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Controller;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservations', name: 'reservations_')]
class ReservationsController extends AbstractController
{
    public function __construct(private ReservationService $reservationService)
    {

    }

    #[Route('', methods: ['GET', 'HEAD'], name: 'list')]
    public function list(PaginatedRequestConfiguration $requestConfiguration): JsonResponse
    {
        $paginator = $this->reservationService->findAllByPaginatedRequest($requestConfiguration);

        return $this->json(
            $paginator,
            Response::HTTP_OK,
            [],
            ['groups' => ['list', 'reservation.list']]
        );
    }
}