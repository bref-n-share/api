<?php

namespace App\Domain\User\Manager\Security;

use App\Domain\User\DTO\AuthenticateUserDTO;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Encoder\PasswordEncoder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SecurityManager
{
    private UserRepositoryInterface $userRepository;
    private PasswordEncoder $passwordEncoder;

    public function __construct(UserRepositoryInterface $userRepository, PasswordEncoder $passwordEncoder)
    {
        $this->userRepository  = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Returns a user token for authentication purposes
     *
     * @param AuthenticateUserDTO $authenticateUserDTO
     *
     * @return string
     *
     * @throws AccessDeniedHttpException
     */
    public function getUserToken(AuthenticateUserDTO $authenticateUserDTO)
    {
        try {
            $user = $this->userRepository->retrieveOneBy([
                'email' => $authenticateUserDTO->email,
            ]);
        } catch (NotFoundHttpException $exception) {
            // throw AccessDenied instead of NotFound because we don't want
            // the user to know if an account for the provided email exists
            throw new AccessDeniedHttpException('Email or Password invalid');
        }

        if (!$this->passwordEncoder->isPasswordValid($user, $authenticateUserDTO->password)) {
            throw new AccessDeniedHttpException('Email or Password invalid');
        }

        // FIXME: Should return a token
        return $user->getId()->toString();
    }
}
