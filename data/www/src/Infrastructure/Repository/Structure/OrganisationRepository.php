<?php

namespace App\Infrastructure\Repository\Structure;

use App\Domain\Structure\Repository\OrganisationRepositoryInterface;
use App\Infrastructure\Entity\Structure\Organisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organisation[]    findAll()
 * @method Organisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganisationRepository extends ServiceEntityRepository implements OrganisationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organisation::class);
    }
}
