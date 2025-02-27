<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Structure;

interface StructureManagerInterface
{
    public function retrieve(string $id): Structure;

    public function retrieveAll(): array;

    public function archive(string $id): void;

    public function getInitialStatus(): string;

    public function supports(Structure $structure): bool;

    public function getFormattedStructureFromMemberCreation(Structure $getStructure): Structure;
}
