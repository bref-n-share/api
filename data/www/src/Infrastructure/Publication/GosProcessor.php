<?php

namespace App\Infrastructure\Publication;

use App\Domain\Post\Entity\Post;
use App\Domain\Post\Publication\PostPublicationProcessorInterface;

class GosProcessor implements PostPublicationProcessorInterface
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
