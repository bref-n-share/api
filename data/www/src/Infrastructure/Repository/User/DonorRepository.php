<?php

namespace App\Infrastructure\Repository\User;

use App\Domain\User\Repository\DonorRepositoryInterface;
use App\Infrastructure\Entity\User\Donor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Donor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donor[]    findAll()
 * @method Donor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonorRepository extends ServiceEntityRepository implements DonorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donor::class);
    }
}
