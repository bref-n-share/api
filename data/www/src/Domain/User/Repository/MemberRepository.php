<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository implements MemberRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Member::class);
    }

    public function save(Member $member): Member
    {
        $this->_em->persist($member);
        $this->_em->flush();

        return $member;
    }

    public function retrieve(string $id): Member
    {
        $entity = $this->find(Uuid::fromString($id));
        if (!$entity) {
            throw new NotFoundHttpException(Member::class . ' not found with id (' . $id . ')');
        }

        return $entity;
    }

    public function delete(string $id): void
    {
        $entity = $this->find(Uuid::fromString($id));
        if (!$entity) {
            throw new NotFoundHttpException(Member::class . ' not found with id (' . $id . ')');
        }

        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
