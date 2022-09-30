<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver;

use App\Model\ContactDTO;
use App\Service\ContactService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactDTOResolver implements ArgumentValueResolverInterface
{
    public function __construct(private ContactService $contactService)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ContactDTO::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $contact = $this->contactService->findOneByIdForCurrentUser((int) $request->get('id'));

        if (!$contact) {
            throw new NotFoundHttpException();
        }

        yield new ContactDTO($contact);
    }
}