<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Repository;

use App\Core\Entity\EntityInterface;
use App\Core\Model\PaginatedRequestConfiguration;
use App\Core\Service\Pagination\Paginator;
use App\Core\Service\Pagination\Page;
use App\Core\Service\Pagination\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class EntityRepositoryAbstract extends ServiceEntityRepository implements RepositoryInterface
{
    public const MAIN_ALIAS = 'o';

    /**
     * @param Page $page
     * @param array $criteria
     * @param array $sorting
     * @param QueryBuilder|null $queryBuilder
     * @return PaginatorInterface
     */
    public function createPaginator(
        Page $page,
        array $criteria = [],
        array $sorting = [],
        ?QueryBuilder $queryBuilder = null
    ): PaginatorInterface
    {
        if (is_null($queryBuilder)) {
            $queryBuilder = $this->createQueryBuilderWithMainAlias();
        }

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return new Paginator($queryBuilder->getQuery(), $page);
    }

    /**
     * @param PaginatedRequestConfiguration $paginatedRequest
     * @return PaginatorInterface
     */
    public function findAllByPaginatedRequest(
        PaginatedRequestConfiguration $paginatedRequest
    ): PaginatorInterface
    {

        return $this->createPaginator(
            $paginatedRequest->getPage(),
            $paginatedRequest->getCriteria(),
            $paginatedRequest->getSorting()
        );
    }

    /**
     * @param $indexBy
     * @return QueryBuilder
     */
    public function createQueryBuilderWithMainAlias($indexBy = null): QueryBuilder
    {
        return $this->createQueryBuilder(self::MAIN_ALIAS, $indexBy);
    }

    /**
     * @param EntityInterface $entity
     * @param bool $flush
     * @return void
     */
    public function add(EntityInterface $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param EntityInterface $entity
     * @param bool $flush
     * @return void
     */
    public function remove(EntityInterface $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function applySorting(QueryBuilder $queryBuilder, array $sorting = []): void
    {
        if (!$sorting) {
            $sorting['createdAt'] = self::ORDER_DESCENDING;
        }

        $entityFields = array_merge($this->_class->getAssociationNames(), $this->_class->getFieldNames());
        foreach ($sorting as $property => $order) {
            if (!in_array($property, $entityFields, true)) {
                continue;
            }

            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property), $order);

                return;
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $criteria
     */
    private function applyCriteria(QueryBuilder $queryBuilder, array $criteria = []): void
    {
        $entityFields = array_merge($this->_class->getAssociationNames(), $this->_class->getFieldNames());

        foreach ($criteria as $property => $value) {
            if (!in_array($property, $entityFields, true) && $property !== 'like') {
                continue;
            }

            $name = $this->getPropertyName($property);

            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
                continue;
            }
            if (is_array($value)) {
                if ($property === 'like' && isset($value['field'], $value['criteria'])) {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->like($this->getPropertyName($value['field']), ':like'))
                        ->setParameter('like', '%' . $value['criteria'] . '%');
                } else {
                    $literalList = [];
                    foreach ($value as $literalItem) {
                        if (is_object($literalItem)) {
                            array_push($literalList, (string)$literalItem);
                        }
                    }
                    $queryBuilder->andWhere($queryBuilder->expr()->in($name, $literalList));
                }
                continue;
            }

            if ((is_scalar($value) && $value !== '') || is_bool($value) || is_object($value)) {
                if ($this->_class->getTypeOfField($property) === 'boolean') {
                    if (is_scalar($value) && $value === 'false') {
                        $value = false;
                    }
                    $value = (bool) $value;
                }
                $parameter = str_replace('.', '_', $property);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($name, ':' . $parameter))
                    ->setParameter($parameter, $value);
            }
        }
    }

    /**
     * @param string $name
     * @return string
     */
    private function getPropertyName(string $name): string
    {
        if (false === strpos($name, '.')) {

            return self::MAIN_ALIAS.'.'.$name;
        }

        return $name;
    }
}