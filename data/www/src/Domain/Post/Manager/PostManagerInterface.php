<?php

namespace App\Domain\Post\Manager;

use App\Domain\Post\Entity\Post;

interface PostManagerInterface
{
    public function create(Post $post): Post;

    public function retrieve(string $id): Post;

    public function retrieveAll(): array;

    public function archive(string $id): void;
}
