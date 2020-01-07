<?php

namespace App\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\PasswordEncoderInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\DonorRepositoryInterface;

class DonorManager extends AbstractUserManager
{
    private DonorRepositoryInterface $donorRepository;

    public function __construct(
        PasswordEncoderInterface $passwordEncoder,
        DonorRepositoryInterface $donorRepository
    ) {
        parent::__construct($passwordEncoder);
        $this->donorRepository = $donorRepository;
    }

    public function create(User $donor): User
    {
        if (!($donor instanceof Donor)) {
            throw new ConflictException('Must be an instance of ' . Donor::class);
        }

        // TODO : Change when the Workflow Component will be set
        $donor->setStatus('DRAFT');

        $donor->setPassword($this->encodePassword($donor, $donor->getPassword()));

        return $this->donorRepository->save($donor);
    }
}
