<?php

namespace App\Core\Service\Pagination;

interface PaginatorInterface
{
    /**
     * @return iterable
     */
    public function getItems(): iterable;

    /**
     * @param array $items
     */
    public function setItems(array $items);

    /**
     * @return MetaData
     */
    public function getMeta(): MetaData;
}