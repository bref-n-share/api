<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Entity\Information;
use App\Domain\Post\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Information|null find($id, $lockMode = null, $lockVersion = null)
 * @method Information|null findOneBy(array $criteria, array $orderBy = null)
 * @method Information[]    findAll()
 * @method Information[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationRepository extends ServiceEntityRepository implements InformationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Information::class);
    }

    public function save(Post $post): Post
    {
        $this->_em->persist($post);
        $this->_em->flush();

        return $post;
    }

    public function retrieve(string $id): Post
    {
        $post = $this->find(Uuid::fromString($id));
        if (!$post) {
            throw new NotFoundHttpException(Post::class . ' not found with id (' . $id . ')');
        }

        return $post;
    }

    public function retrieveAll(): array
    {
        return $this->findAll();
    }

    /**
     * @param string $id
     *
     * @return Post[]
     */
    public function retrieveAllBySite(string $id): array
    {
        return $this->findBy(['site' => Uuid::fromString($id)]);
    }
}
