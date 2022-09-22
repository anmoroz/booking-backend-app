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