<?php

namespace App\Domain\User\Workflow;

use App\Domain\User\Entity\User;

interface UserWorkflowProcessorInterface
{
    public function can(User $entity, string $action): bool;

    public function apply(User $entity, string $action);

    public function getInitialStatus(): string;
}
