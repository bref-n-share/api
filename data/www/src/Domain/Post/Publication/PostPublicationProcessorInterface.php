<?php

namespace App\Domain\Post\Publication;

use App\Domain\Post\Entity\Post;

interface PostPublicationProcessorInterface
{
    public function supports(string $channel): bool;

    public function handle(Post $post): bool;
}
