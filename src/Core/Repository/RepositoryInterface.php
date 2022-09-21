<?php

namespace App\Core\Repository;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Core\Service\Pagination\Page;
use App\Core\Service\Pagination\PaginatorInterface;

interface RepositoryInterface
{
    public const ORDER_ASCENDING = 'ASC';
    public const ORDER_DESCENDING = 'DESC';

    public function createPaginator(Page $page, array $criteria = [], array $sorting = []): PaginatorInterface;

    public function findAllByPaginatedRequest(PaginatedRequestConfiguration $paginatedRequestConfiguration): PaginatorInterface;
}