<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Service\Pagination;

use Doctrine\ORM\Query;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Annotation\Groups;

class Paginator implements PaginatorInterface
{
    #[Groups(["show", "list"])]
    private iterable $items;

    #[Groups(["show", "list"])]
    private MetaData $meta;

    public function __construct(Query $query, Page $page)
    {
        $adapter = new QueryAdapter($query, false, false);

        $this->paginate($adapter, $page);
    }

    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->items;
    }

    /**
     * @return MetaData
     */
    public function getMeta(): MetaData
    {
        return $this->meta;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param AdapterInterface $adapter
     * @param Page $page
     */
    protected function paginate(AdapterInterface $adapter, Page $page)
    {
        try {
            $pagerfanta = new Pagerfanta($adapter);
            $perPage = $page->getPerPage();
            $currentPage = $page->getPage();
            // При perPage = -1 пагинация отключается
            if ($page->getPerPage() === -1) {
                $perPage = Page::PER_PAGE_IF_PAGINATION_OFF;
                $currentPage = 1;
            }
            $pagerfanta->setMaxPerPage($perPage);
            $pagerfanta->setCurrentPage($currentPage);
        } catch (NotValidCurrentPageException) {
            throw new NotFoundHttpException();
        }

        $this->meta = new MetaData(
            $pagerfanta->getNbResults(),
            $pagerfanta->getNbPages(),
            $pagerfanta->getCurrentPage(),
            $pagerfanta->getMaxPerPage()
        );

        $this->createItems($pagerfanta);
    }

    /**
     * @param Pagerfanta $pagerfanta
     */
    protected function createItems(Pagerfanta $pagerfanta)
    {
        $this->items = $pagerfanta->getCurrentPageResults();
    }
}