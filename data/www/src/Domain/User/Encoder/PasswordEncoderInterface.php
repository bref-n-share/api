<?php

namespace App\Domain\User\Entity;

interface PasswordEncoderInterface
{
    public function encodePassword(User $user, string $password): string;
}
