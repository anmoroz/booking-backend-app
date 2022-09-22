<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Model;

use App\Core\Service\Pagination\Page;

class PaginatedRequestConfiguration
{
    private Page $page;

    private CriteriaRequest $criteriaRequest;

    private array $sorting;

    /**
     * @param int $page
     * @param int $perPage
     * @param array $criteria
     * @param array $sorting
     */
    public function __construct(int $page, int $perPage, array $criteria = [], array $sorting = [])
    {
        if ($page < 1) {
            $page = Page::DEFAULT_CURRENT_PAGE;
        }

        if ($perPage !== -1 && ($perPage > Page::MAX_PER_PAGE || $perPage < 0)) {
            $perPage = Page::DEFAULT_PER_PAGE;
        }

        $this->page = new Page($page, $perPage);
        $this->criteriaRequest = new CriteriaRequest($criteria);
        $this->sorting = $sorting;
    }

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @return array
     */
    public function getCriteria(): array
    {
        return $this->criteriaRequest->getCriteria();
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getCriteriaByKey(string $key)
    {
        return $this->criteriaRequest->getCriteriaByKey($key);
    }

    /**
     * @return string
     */
    public function getCriteriaKeyword(): string
    {
        return $this->criteriaRequest->getCriteriaKeyword();
    }

    /**
     * @return array
     */
    public function getSorting(): array
    {
        return $this->sorting;
    }

    /**
     * @param string $key
     * @param object|array|string|int|float|bool $value
     */
    public function addCriteria(string $key, $value): void
    {
        $this->criteriaRequest->addCriteria($key, $value);
    }

    /**
     * @param array $sorting
     */
    public function setSorting(array $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @param string $key
     */
    public function removeCriteria(string $key): void
    {
        $this->criteriaRequest->removeCriteria($key);
    }
}