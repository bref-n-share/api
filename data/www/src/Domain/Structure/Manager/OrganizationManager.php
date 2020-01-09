<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Organization;
use App\Domain\Structure\Entity\Structure;

class OrganizationManager extends AbstractStructureManager
{
    public function supports(Structure $structure): bool
    {
        return $structure instanceof Organization;
    }
}
