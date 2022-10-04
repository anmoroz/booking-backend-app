<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\ArgumentResolver;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Core\Service\Pagination\Page;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaginatedRequestConfigurationResolver extends RequestArgumentResolverAbstract
{

    public function __construct(private RoomRepository $roomRepository)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return PaginatedRequestConfiguration::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $page = $this->getIntegerQueryParameter($request, 'page', Page::DEFAULT_CURRENT_PAGE);
        $perPage = $this->getIntegerQueryParameter($request, 'perPage', Page::DEFAULT_PER_PAGE);
        $criteria = $this->getArrayQueryParameter($request, 'criteria', []);
        $sorting = $this->getArrayQueryParameter($request, 'sorting', []);

        if (isset($criteria['roomId']) && $criteria['roomId']) {
            $room = $this->roomRepository->find((int) $criteria['roomId']);
            if (!$room) {
                throw new NotFoundHttpException('Объект размещения не найден');
            }
            unset($criteria['roomId']);
            $criteria['room'] = $room;
        }

        yield new PaginatedRequestConfiguration(
            $page,
            $perPage,
            $criteria,
            $sorting
        );
    }
}