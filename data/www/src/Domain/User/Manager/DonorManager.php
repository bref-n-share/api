<?php

namespace App\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Structure\Entity\Site;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\User;

class DonorManager extends AbstractUserManager
{
    public function create(User $donor): User
    {
        if (!($donor instanceof Donor)) {
            throw new ConflictException('Must be an instance of ' . Donor::class);
        }

        $donor->setStatus($this->workflowProcessor->getInitialStatus());

        $donor->setPassword($this->encodePassword($donor, $donor->getPassword()));

        return $this->userRepository->save($donor);
    }

    public function addFavorite(Donor $donor, Site $site): void
    {
        $donor->addSite($site);
        $this->userRepository->save($donor);
    }

    public function removeFavorite(Donor $donor, Site $site): void
    {
        $donor->removeSite($site);
        $this->userRepository->save($donor);
    }
}
