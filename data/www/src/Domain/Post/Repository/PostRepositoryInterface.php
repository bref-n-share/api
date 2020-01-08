<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Entity\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): Post;

    public function retrieve(string $id): Post;

    /**
     * @return Post[]
     */
    public function retrieveAll(): array;
}
