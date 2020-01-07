<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Structure;
use App\Domain\Structure\Repository\StructureRepositoryInterface;

class StructureManager implements StructureManagerInterface
{
    private StructureRepositoryInterface $structureRepository;

    public function __construct(StructureRepositoryInterface $structureRepository)
    {
        $this->structureRepository = $structureRepository;
    }

    public function retrieve(string $id): Structure
    {
        return $this->structureRepository->retrieve($id);
    }
}
