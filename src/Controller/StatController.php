<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Controller;

use App\Model\RoomDTO;
use App\Service\StatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stat', name: 'stat_')]
class StatController extends AbstractController
{
    public function __construct(private StatService $statService)
    {
    }

    #[Route('/room/{id}/reservations', methods: ['GET', 'HEAD'], name: 'reservations')]
    public function reservations(RoomDTO $roomDTO): JsonResponse
    {
        $items = $this->statService->reservationStatistics($roomDTO->getRoom(), (int) date("Y"));

        return $this->json(
            ['items' => $items],
            Response::HTTP_OK,
            [],
            ['groups' => ['show']]
        );
    }
}