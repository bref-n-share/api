<?php

namespace App\Infrastructure\Notification;

use App\Domain\Post\Entity\Post;
use App\Domain\Post\Notification\NotificationProcessorInterface;

class GosProcessor implements NotificationProcessorInterface
{
    private const TYPE = 'gos';

    public function supports(string $channel): bool
    {
        return strtolower($channel) === strtolower(self::TYPE);
    }

    public function handle(Post $post): bool
    {
        // Posting in the GoS is just adding 'GOS' to the Post's channels
        return true;
    }
}
