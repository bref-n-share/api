<?php

namespace App\Domain\User\Manager;

use App\Domain\User\Entity\User;

interface UserManagerInterface
{
    public function create(User $user): User;

    public function retrieve(string $id): User;

    public function delete(string $id): void;

    public function encodePassword(User $user, string $password): string;
}
