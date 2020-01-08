<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\Donor;

interface DonorRepositoryInterface
{
    public function save(Donor $donor): Donor;

    public function retrieve(string $id): Donor;
}
