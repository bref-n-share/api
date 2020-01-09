<?php

namespace App\Domain\User\DTO;

class AuthenticateUserDTO
{
    /** @var string */
    public string $email;

    /** @var string */
    public string $password;
}
