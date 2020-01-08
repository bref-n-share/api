<?php

namespace App\Domain\Structure\Workflow;

use App\Domain\Structure\Entity\Structure;

interface StructureWorkflowProcessorInterface
{
    public function can(Structure $entity, string $action): bool;

    public function apply(Structure $entity, string $action);

    public function getInitialStatus(): string;
}
