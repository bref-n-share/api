<?php

namespace App\Infrastructure\Workflow\Structure;

use App\Domain\Structure\Entity\Structure;
use App\Domain\Structure\Workflow\StructureWorkflowProcessorInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\StateMachine;

class StructureWorkflowProcessor implements StructureWorkflowProcessorInterface
{
    private StateMachine $stateMachine;

    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    public function can(Structure $entity, string $action): bool
    {
        return $this->stateMachine->can($entity, $action);
    }

    public function apply(Structure $entity, string $action): void
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
