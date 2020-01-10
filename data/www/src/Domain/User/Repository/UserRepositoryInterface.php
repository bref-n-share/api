<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): User;

    public function retrieve(string $id): User;

    public function retrieveOneBy(array $criteria): User;
}
