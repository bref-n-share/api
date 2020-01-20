<?php

namespace App\Domain\User\Manager;

use App\Domain\User\DTO\UserEdit;
use App\Domain\User\Entity\User;

interface UserManagerInterface
{
    public function create(User $user): User;

    public function retrieve(string $id): User;

    public function archive(string $id): void;

    public function encodePassword(User $user, string $password): string;

    public function supports(User $user): bool;

    public function getUpdatedEntity(UserEdit $entity, string $id): User;
}
