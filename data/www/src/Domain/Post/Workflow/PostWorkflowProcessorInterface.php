<?php

namespace App\Domain\Post\Workflow;

use App\Domain\Post\Entity\Post;

interface PostWorkflowProcessorInterface
{
    public function can(Post $entity, string $action): bool;

    public function apply(Post $entity, string $action);

    public function getInitialStatus(): string;
}
