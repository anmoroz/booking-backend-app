<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Controller;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Model\ContactDetails;
use App\Model\ContactDTO;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contacts', name: 'contacts_')]
class ContactsController extends AbstractController
{
    public function __construct(
        private ContactService $contactService
    )
    {
    }

    #[Route('', methods: ['GET', 'HEAD'], name: 'list')]
    public function list(PaginatedRequestConfiguration $requestConfiguration): JsonResponse
    {
        $paginator = $this->contactService->findAllByPaginatedRequest($requestConfiguration);

        return $this->json($paginator, Response::HTTP_OK, [], ['groups' => ['list', 'contact.list']]);
    }

    #[Route('/{id}', methods: ['GET', 'HEAD'], name: 'show')]
    public function show(ContactDTO $contactDTO): JsonResponse
    {
        return $this->json(
            $contactDTO->getContact(),
            Response::HTTP_OK,
            [],
            ['groups' => ['show', 'contact.list']]
        );
    }

    #[Route('', methods: ['POST'], name: 'create')]
    public function create(ContactDetails $contactDetails): JsonResponse
    {
        $contact = $this->contactService->create($contactDetails);

        return $this->json($contact, Response::HTTP_OK, [], ['groups' => ['show', 'contact.list']]);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(ContactDTO $contactDTO, ContactDetails $contactDetails): JsonResponse
    {
        $contact = $this->contactService->update($contactDTO->getContact(), $contactDetails);

        return $this->json($contact, Response::HTTP_OK, [], ['groups' => ['show', 'contact.list']]);
    }
}