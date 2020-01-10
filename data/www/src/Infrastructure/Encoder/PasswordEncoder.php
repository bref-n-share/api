<?php

namespace App\Infrastructure\Encoder;

use App\Domain\User\Encoder\PasswordEncoderInterface;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoder implements PasswordEncoderInterface
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function encodePassword(User $user, string $password): string
    {
        return $this->userPasswordEncoder->encodePassword($user, $password);
    }

    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $password);
    }
}
