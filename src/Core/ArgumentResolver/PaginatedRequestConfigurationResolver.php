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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PaginatedRequestConfigurationResolver extends RequestArgumentResolverAbstract
{
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

        yield new PaginatedRequestConfiguration(
            $page,
            $perPage,
            $criteria,
            $sorting
        );
    }
}