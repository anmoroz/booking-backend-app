<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver;

use App\Model\RoomDTO;
use App\Service\RoomService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoomDTOResolver implements ArgumentValueResolverInterface
{
    public function __construct(private RoomService $roomService)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RoomDTO::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $room = $this->roomService->findOneByIdForCurrentUser((int) $request->get('id'));

        if (!$room) {
            throw new NotFoundHttpException();
        }

        yield new RoomDTO($room);
    }
}