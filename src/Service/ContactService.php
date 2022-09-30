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
        $currentUser = $this->userService->getCurrentUser();
        $paginatedRequest->addCriteria('user', $currentUser);

        return $this->contactRepository->findAllByPaginatedRequest($paginatedRequest);
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