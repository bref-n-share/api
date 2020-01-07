<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Structure;

interface StructureManagerInterface
{
    public function retrieve(string $id): Structure;
}