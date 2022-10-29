<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Core\Service\Pagination\PaginatorInterface;
use App\Entity\Contact;
use App\Model\ContactDetails;
use App\Repository\ContactRepository;
use Doctrine\ORM\Query\Expr\Join;

class ContactService
{
    public function __construct(
        private ContactRepository $contactRepository,
        private UserService $userService
    )
    {
    }

    /**
     * @param int $id
     * @return Contact|null
     */
    public function findOneByIdForCurrentUser(int $id): ?Contact
    {
        return $this->contactRepository->findOneBy([
            'id' => $id,
            'user' => $this->userService->getCurrentUser()
        ]);
    }

    /**
     * @param PaginatedRequestConfiguration $paginatedRequest
     * @return PaginatorInterface
     */
    public function findAllByPaginatedRequest(PaginatedRequestConfiguration $paginatedRequest): PaginatorInterface
    {
        $queryBuilder = $this->contactRepository->createQueryBuilderWithMainAlias();
        $queryBuilder->select(['o', 'lastReservation']);
        $queryBuilder->leftJoin(
            'o.reservations',
            'lastReservation',
            Join::WITH,
            'o.id = lastReservation.contact AND lastReservation.checkin = (
                SELECT MAX(r2.checkin)
                FROM \App\Entity\Reservation AS r2
                WHERE r2.contact = o.id
            )'
        );
        $currentUser = $this->userService->getCurrentUser();
        $paginatedRequest->addCriteria('user', $currentUser);

        $searchTerm = $paginatedRequest->getCriteriaKeyword();

        if (preg_match('/^[\d\(\)\-\s+]*$/', $searchTerm)) {
            $searchTerm = preg_replace('/[\(\)\-\s+]/', '', $searchTerm);
        }
        if ($searchTerm) {
            $queryBuilder
                ->andWhere('
                    o.name LIKE :searchTerm 
                    OR o.phone LIKE :searchTerm
                ')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');

        }

        return $this->contactRepository->createPaginator(
            $paginatedRequest->getPage(),
            $paginatedRequest->getCriteria(),
            $paginatedRequest->getSorting(),
            $queryBuilder
        );
    }

    /**
     * @param Contact $contact
     * @param ContactDetails $contactDetails
     * @return Contact
     */
    public function update(Contact $contact, ContactDetails $contactDetails): Contact
    {
        $contact
            ->setPhone($contactDetails->getPhone())
            ->setName($contactDetails->getName())
            ->setNote($contactDetails->getNote())
            ->setIsBanned($contactDetails->isBanned());

        $this->contactRepository->add($contact, true);

        return $contact;
    }

    /**
     * @param string $phone
     * @return Contact|null
     */
    public function findOneByPhone(string $phone): ?Contact
    {
        return $this->contactRepository->findOneByPhone(
            $phone,
            $this->userService->getCurrentUser()
        );
    }

    /**
     * @param ContactDetails $contactDetails
     * @return Contact
     */
    public function create(ContactDetails $contactDetails): Contact
    {
        $contact = new Contact();
        $contact
            ->setUser($this->userService->getCurrentUser())
            ->setCreatedAtNow()
            ->setPhone($contactDetails->getPhone())
            ->setName($contactDetails->getName())
            ->setNote($contactDetails->getNote())
            ->setIsBanned($contactDetails->isBanned());

        $this->contactRepository->add($contact, true);

        return $contact;
    }
}