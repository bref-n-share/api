<?php

namespace App\Domain\User\Manager;

use App\Domain\User\Entity\User;

interface UserManagerInterface
{
    public function create(User $user): User;

    public function encodePassword(User $user, string $password): string;
}
