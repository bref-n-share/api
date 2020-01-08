<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Structure;
use App\Domain\Structure\Repository\StructureRepositoryInterface;

class StructureManager
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

    /**
     * @return Structure[]
     */
    public function retrieveAll(): array
    {
        return $this->structureRepository->retrieveAll();
    }

    public function save(Structure $structure): Structure
    {
        return $this->structureRepository->save($structure);
    }
}
