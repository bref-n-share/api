<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\Donor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function save(Donor $donor): Donor
    {
        $this->_em->persist($donor);
        $this->_em->flush();

        return $donor;
    }

    public function retrieve(string $id): Donor
    {
        $entity = $this->find(Uuid::fromString($id));
        if (!$entity) {
            throw new NotFoundHttpException(Donor::class . ' not found with id (' . $id . ')');
        }

        return $entity;
    }

    public function delete(string $id): void
    {
        $entity = $this->find(Uuid::fromString($id));
        if (!$entity) {
            throw new NotFoundHttpException(Donor::class . ' not found with id (' . $id . ')');
        }

        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
