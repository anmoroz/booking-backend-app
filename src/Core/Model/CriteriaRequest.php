<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Model;

class CriteriaRequest
{
    private const KEYWORD_CRITERIA_NAME = 'keyword';

    private array $criteria;

    public function __construct(array $criteria = [])
    {
        if (isset($criteria[self::KEYWORD_CRITERIA_NAME]) && trim((string) $criteria[self::KEYWORD_CRITERIA_NAME]) === '') {
            unset($criteria[self::KEYWORD_CRITERIA_NAME]);
        }

        $this->criteria = $criteria;
    }

    /**
     * @return array
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * @param string $key
     * @param object|array|string|int|float|bool $value
     */
    public function addCriteria(string $key, $value): void
    {
        $this->criteria[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getCriteriaByKey(string $key)
    {
        return $this->criteria[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getCriteriaKeyword(): string
    {
        return trim((string) ($this->criteria[self::KEYWORD_CRITERIA_NAME] ?? ''));
    }

    /**
     * @param string $key
     */
    public function removeCriteria(string $key): void
    {
        if (isset($this->criteria[$key])) {
            unset($this->criteria[$key]);
        }
    }
}