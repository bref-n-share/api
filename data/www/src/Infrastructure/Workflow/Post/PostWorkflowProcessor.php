<?php

namespace App\Infrastructure\Workflow\Post;

use App\Domain\Post\Entity\Post;
use App\Domain\Post\Workflow\PostWorkflowProcessorInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\StateMachine;

class PostWorkflowProcessor implements PostWorkflowProcessorInterface
{
    private StateMachine $stateMachine;

    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    public function can(Post $entity, string $action): bool
    {
        return $this->stateMachine->can($entity, $action);
    }

    public function apply(Post $entity, string $action): void
    {
        $this->stateMachine->apply($entity, $action);
    }

    public function getInitialStatus(): string
    {
        if (!isset($this->stateMachine->getDefinition()->getInitialPlaces()[0])) {
            throw new LogicException("No initial place is defined");
        }

        return $this->stateMachine->getDefinition()->getInitialPlaces()[0];
    }
}
