<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Core\Service\Pagination\PaginatorInterface;
use App\Entity\Room;
use App\Model\RoomDetails;
use App\Repository\RoomRepository;

class RoomService
{
    public function __construct(
        private RoomRepository $roomRepository,
        private UserService    $userService
    )
    {
    }

    /**
     * @param Room $room
     * @param RoomDetails $roomDetails
     * @return Room
     */
    public function update(Room $room, RoomDetails $roomDetails): Room
    {
        $room
            ->setName($roomDetails->getName())
            ->setAddress($roomDetails->getAddress());

        $this->roomRepository->add($room, true);

        return $room;
    }

    /**
     * @param RoomDetails $roomDetails
     * @return Room
     */
    public function create(RoomDetails $roomDetails): Room
    {
        $room = new Room();
        $room
            ->setUser($this->userService->getCurrentUser())
            ->setName($roomDetails->getName())
            ->setAddress($roomDetails->getAddress());

        $this->roomRepository->add($room, true);

        return $room;
    }

    /**
     * @param PaginatedRequestConfiguration $paginatedRequest
     * @return PaginatorInterface
     */
    public function findAllByPaginatedRequest(PaginatedRequestConfiguration $paginatedRequest): PaginatorInterface
    {
        $currentUser = $this->userService->getCurrentUser();
        $paginatedRequest->addCriteria('user', $currentUser);

        return $this->roomRepository->findAllByPaginatedRequest($paginatedRequest);
    }

    /**
     * @param int $id
     * @return Room|null
     */
    public function findOneByIdForCurrentUser(int $id): ?Room
    {
        return $this->roomRepository->findOneBy([
            'id' => $id,
            'user' => $this->userService->getCurrentUser()
        ]);
    }
}