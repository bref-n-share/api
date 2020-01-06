<?php

namespace App\Infrastructure\Repository\Structure;

use App\Domain\Structure\Repository\SiteRepositoryInterface;
use App\Infrastructure\Entity\Structure\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository implements SiteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }
}
