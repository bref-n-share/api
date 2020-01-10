<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): User
    {
        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function retrieve(string $id): User
    {
        $user = $this->find(Uuid::fromString($id));
        if (!$user) {
            throw new NotFoundHttpException(User::class . ' not found with id (' . $id . ')');
        }

        return $user;
    }

    public function retrieveOneBy(array $criteria): User
    {
        $user = $this->findOneBy($criteria);
        if (!$user) {
            throw new NotFoundHttpException(User::class . ' not found');
        }

        return $user;
    }
}
