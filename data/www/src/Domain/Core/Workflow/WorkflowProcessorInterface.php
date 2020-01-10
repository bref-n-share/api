<?php

namespace App\Domain\Core\Workflow;

interface WorkflowProcessorInterface
{
    public function can($entity, string $action): bool;

    public function apply($entity, string $action);

    public function getInitialStatus(): string;
}
