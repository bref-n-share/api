<?php

namespace App\Domain\Post\Notification;

use App\Domain\Post\Entity\Post;

interface PostNotificationProcessorInterface
{
    public function supports(string $channel): bool;

    public function handle(Post $post): bool;
}
