<?php

namespace App\Domain\Structure\Repository;

use App\Domain\Structure\Entity\Structure;

interface StructureRepositoryInterface
{
    public function retrieve(string $id): Structure;
}
