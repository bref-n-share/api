<?php

namespace App\Domain\User\Manager;

use App\Domain\User\Entity\PasswordEncoderInterface;
use App\Domain\User\Entity\User;

abstract class AbstractUserManager implements UserManagerInterface
{
    private PasswordEncoderInterface $passwordEncoder;

    public function __construct(PasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodePassword(User $user, string $password): string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }
}
