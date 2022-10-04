<?php

namespace App\Controller;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Model\ReservationDetails;
use App\Model\RoomDetails;
use App\Model\RoomDTO;
use App\Service\ReservationService;
use App\Service\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rooms', name: 'rooms_')]
class RoomsController extends AbstractController
{
    public function __construct(
        private RoomService $roomService,
        private ReservationService $reservationService
    )
    {
    }

    #[Route('', methods: ['GET', 'HEAD'], name: 'list')]
    public function list(PaginatedRequestConfiguration $requestConfiguration): JsonResponse
    {
        $paginator = $this->roomService->findAllByPaginatedRequest($requestConfiguration);

        return $this->json($paginator, Response::HTTP_OK, [], ['groups' => ['room.list', 'list']]);
    }

    #[Route('', methods: ['POST'], name: 'create')]
    public function create(RoomDetails $roomDetails): JsonResponse
    {
        $room = $this->roomService->create($roomDetails);

        return $this->json($room, Response::HTTP_OK, [], ['groups' => ['room.list', 'show']]);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(RoomDTO $roomDTO, RoomDetails $roomDetails): JsonResponse
    {
        $room = $this->roomService->update($roomDTO->getRoom(), $roomDetails);

        return $this->json($room, Response::HTTP_OK, [], ['groups' => ['room.list', 'show']]);
    }

    #[Route('/{id}', methods: ['GET', 'HEAD'], name: 'show')]
    public function show(RoomDTO $roomDTO): JsonResponse
    {
        return $this->json(
            $roomDTO->getRoom(),
            Response::HTTP_OK,
            [],
            ['groups' => ['room.list',  'show']]
        );
    }

    #[Route('/{id}/reservations', methods: ['GET', 'HEAD'], name: 'reservations')]
    public function reservations(
        RoomDTO $roomDTO,
        PaginatedRequestConfiguration $requestConfiguration
    ): JsonResponse
    {
        $paginator = $this->reservationService->findAll(
            $roomDTO->getRoom(),
            $requestConfiguration
        );

        return $this->json(
            $paginator,
            Response::HTTP_OK,
            [],
            ['groups' => ['reservation.list', 'list']]
        );
    }

    #[Route('/{id}/reservations', methods: ['POST'], name: 'reservations.create')]
    public function createReservation(
        RoomDTO $roomDTO,
        ReservationDetails $reservationDetails
    ): JsonResponse
    {
        $reservation = $this->reservationService->create($roomDTO->getRoom(), $reservationDetails);

        return $this->json(
            $reservation,
            Response::HTTP_OK,
            [],
            ['groups' => ['reservation.list', 'show']]
        );
    }

    #[Route('/{id}/reservations/{reservationId}', methods: ['PUT'], name: 'reservations.update')]
    public function updateReservation(
        RoomDTO $roomDTO,
        int $reservationId,
        ReservationDetails $reservationDetails
    ): JsonResponse
    {
        $reservation = $this->reservationService->findReservationById($reservationId, $roomDTO->getRoom());
        if (!$reservation) {
            throw new NotFoundHttpException();
        }

        $this->reservationService->update($roomDTO->getRoom(), $reservation, $reservationDetails);

        return $this->json(
            $reservation,
            Response::HTTP_OK,
            [],
            ['groups' => ['reservation.list', 'show']]
        );
    }

    #[Route('/{id}/reservations/{reservationId}', methods: ['DELETE'], name: 'reservations.delete')]
    public function deleteReservation(
        RoomDTO $roomDTO,
        int $reservationId
    ): JsonResponse
    {
        $reservation = $this->reservationService->findReservationById($reservationId, $roomDTO->getRoom());
        if (!$reservation) {
            throw new NotFoundHttpException();
        }

        $this->reservationService->delete($reservation);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
