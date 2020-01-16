<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Entity\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): Post;

    public function retrieve(string $id): Post;

    /**
     * @param array $options
     * @return Post[]
     */
    public function retrieveBy(array $options): array;

    /**
     * @return Post[]
     */
    public function retrieveAll(): array;
}
