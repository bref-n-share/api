<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Entity\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return Category[]
     */
    public function retrieveAll(): array;
}
