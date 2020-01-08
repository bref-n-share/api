<?php

namespace App\Domain\Structure\Repository;

use App\Domain\Structure\Entity\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Structure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Structure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Structure[]    findAll()
 * @method Structure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StructureRepository extends ServiceEntityRepository implements StructureRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Structure::class);
    }

    public function retrieve(string $id): Structure
    {
        $entity = $this->find(Uuid::fromString($id));
        if (!$entity) {
            throw new NotFoundHttpException(Structure::class . ' not found with id (' . $id . ')');
        }

        return $entity;
    }

    public function retrieveAll(): array
    {
        return $this->findAll();
    }
}
