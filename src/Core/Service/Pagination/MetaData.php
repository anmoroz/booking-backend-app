<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Service\Pagination;

use Symfony\Component\Serializer\Annotation\Groups;

class MetaData
{
    #[Groups(["show", "list"])]
    public int $totalCount;

    #[Groups(["show", "list"])]
    public int $pageCount;

    #[Groups(["show", "list"])]
    public int $page;

    #[Groups(["show", "list"])]
    public int $perPage;

    /**
     * @param int $totalCount
     * @param int $pageCount
     * @param int $page
     * @param int $perPage
     */
    public function __construct(int $totalCount, int $pageCount, int $page, int $perPage)
    {
        $this->totalCount = $totalCount;
        $this->pageCount = $pageCount;
        $this->page = $page;
        $this->perPage = $perPage;
    }
}