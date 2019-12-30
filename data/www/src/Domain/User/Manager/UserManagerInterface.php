<?php

namespace App\Domain\User\Manager;

use App\Domain\User\Entity\User;

interface UserManagerInterface
{
    public function create(User $userDTO): User;
}
