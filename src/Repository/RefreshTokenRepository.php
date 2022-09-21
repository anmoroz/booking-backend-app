<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Repository;

use App\Helper\RandomStringGenerator;
use App\Entity\User;
use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 *
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function add(RefreshToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RefreshToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param User $user
     * @return RefreshToken
     */
    public function createByUser(User $user): RefreshToken
    {
        $dql = 'DELETE FROM App\Entity\RefreshToken u WHERE u.user = :user';
        $this->_em->createQuery($dql)
            ->setParameter('user', $user)
            ->execute();

        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user)
            ->setToken(RandomStringGenerator::generate(80))
            ->setExpiresAt((new DateTime())->modify('+'.RefreshToken::LIFE_TIME_DAYS.' day'));

        $this->add($refreshToken, true);

        return $refreshToken;
    }
}
