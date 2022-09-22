<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Repository;

use App\Core\Repository\EntityRepositoryAbstract;
use App\Entity\Contact;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepositoryAbstract<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends EntityRepositoryAbstract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @param string $phone
     * @return Contact|null
     */
    public function findOneByPhone(string $phone): ?Contact
    {
        return $this->findOneBy(['phone' => $phone]);
    }
}
