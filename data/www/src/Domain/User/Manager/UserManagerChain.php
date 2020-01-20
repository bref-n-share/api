<?php

namespace App\Domain\User\Manager;

use App\Domain\User\Entity\User;

class UserManagerChain
{
    /**
     * @var UserManagerInterface[]
     */
    private iterable $managers;

    /**
     * UserManagerChain constructor.
     *
     * @param iterable $managers
     */
    public function __construct(iterable $managers)
    {
        $this->managers = $managers;
    }

    public function getManager(User $user): UserManagerInterface
    {
        foreach ($this->managers as $manager) {
            if ($manager->supports($user)) {
                return $manager;
            }
        }

        throw new \LogicException("No manager supports " . get_class($user));
    }
}
