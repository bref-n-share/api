<?php

namespace App\Domain\Structure\Repository;

use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Entity\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function retrieve(string $id): Structure
    {
        $entity = $this->find(Uuid::fromString($id));
        if (!$entity) {
            throw new NotFoundHttpException(Site::class . ' not found with id (' . $id . ')');
        }

        return $entity;
    }

    public function retrieveAll(): array
    {
        return $this->findAll();
    }

    public function save(Structure $structure): Structure
    {
        $this->_em->persist($structure);
        $this->_em->flush();

        return $structure;
    }
}
