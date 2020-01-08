<?php

namespace App\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\User;

class DonorManager extends AbstractUserManager
{
    public function create(User $donor): User
    {
        if (!($donor instanceof Donor)) {
            throw new ConflictException('Must be an instance of ' . Donor::class);
        }

        // TODO : Change when the Workflow Component will be set
        $donor->setStatus($this->workflowProcessor->getInitialStatus());

        $donor->setPassword($this->encodePassword($donor, $donor->getPassword()));

        return $this->userRepository->save($donor);
    }
}
