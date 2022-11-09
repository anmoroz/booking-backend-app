<?php

namespace App\Repository;

use App\Core\Repository\EntityRepositoryAbstract;
use App\Entity\User;
use App\Entity\UserCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCode>
 *
 * @method UserCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCode[]    findAll()
 * @method UserCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCodeRepository extends  EntityRepositoryAbstract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCode::class);
    }

    /**
     * @param User $user
     * @param int $type
     * @return UserCode|null
     */
    public function findByUserAndType(User $user, int $type): ?UserCode
    {
        return $this->findOneBy(['user' => $user, 'type' => $type]);
    }

    /**
     * @param string $code
     * @return UserCode|null
     */
    public function findOneByCode(string $code): ?UserCode
    {
        return $this->findOneBy(['code' => $code]);
    }
}
