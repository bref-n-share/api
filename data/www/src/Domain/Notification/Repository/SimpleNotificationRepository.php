<?php

namespace App\Domain\Notification\Repository;

use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Entity\SimpleNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SimpleNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimpleNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimpleNotification[]    findAll()
 * @method SimpleNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimpleNotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimpleNotification::class);
    }

    public function save(Notification $notification): Notification
    {
        $this->_em->persist($notification);
        $this->_em->flush();

        return $notification;
    }
}
