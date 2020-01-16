<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    private const KEYS = [
        'title',
        'site',
        'channels'
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
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
     * @param array $options
     * @return Post[]
     */
    public function retrieveBy(array $options): array
    {
        $qb = $this->createQueryBuilder('r');
        foreach ($options as $key => $value) {
            // Ignore unauthorized key
            if (!in_array($key, self::KEYS)) {
                continue;
            }

            if ('channels' === $key || 'title' === $key) {
                $qb->andWhere($qb->expr()->like('r.' . $key, ':' . $key));
                $qb->setParameter($key, '%' . (is_array($value) ? $value[0] : $value) . '%');
                continue;
            }

            $qb->andWhere($qb->expr()->in('r.' . $key, ':' . $key));
            $qb->setParameter($key, $value);
        }

        return $qb->getQuery()->getResult();
    }
}
