<?php

namespace App\Domain\Notification\Manager;

use App\Domain\Notification\DTO\SimpleNotificationCreate;
use App\Domain\Notification\Entity\PostNotification;
use App\Domain\Notification\Entity\SimpleNotification;
use App\Domain\Post\Entity\Post;

class NotificationFactory
{
    private const EXPIRATION_INTERVAL = 'P3D';

    public function createPostNotification(Post $post, \DateTimeImmutable $expirationDate = null): PostNotification
    {
        return (new PostNotification())
            ->setPost($post)
            ->setTitle($post->getTitle())
            ->setDescription($post->getDescription())
            ->setSite($post->getSite())
            ->setExpirationDate($expirationDate ?? (new \DateTime())->add(new \DateInterval(self::EXPIRATION_INTERVAL)))
        ;
    }

    public function createSimpleNotification(SimpleNotificationCreate $simpleNotificationCreate): SimpleNotification
    {
        return (new SimpleNotification())
            ->setTitle($simpleNotificationCreate->getTitle())
            ->setDescription($simpleNotificationCreate->getDescription())
            ->setSite($simpleNotificationCreate->getSite())
            ->setExpirationDate(
                $simpleNotificationCreate->getExpirationDate() ?? (new \DateTime())->add(new \DateInterval(self::EXPIRATION_INTERVAL))
            )
        ;
    }
}
