<?php

namespace App\Domain\Notification\Repository;

use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Entity\PostNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostNotification[]    findAll()
 * @method PostNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostNotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostNotification::class);
    }

    public function save(Notification $notification): Notification
    {
        $this->_em->persist($notification);
        $this->_em->flush();

        return $notification;
    }

    public function retrieveAll(): array
    {
        return $this->findAll();
    }
}
