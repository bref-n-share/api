<?php

namespace App\Infrastructure\Workflow;

use App\Domain\Post\Workflow\WorkflowProcessorInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\StateMachine;

class WorkflowProcessor implements WorkflowProcessorInterface
{
    private StateMachine $stateMachine;

    private string $type;

    public function __construct(StateMachine $stateMachine, string $type)
    {
        $this->stateMachine = $stateMachine;
        $this->type = $type;
    }

    public function can($entity, string $action): bool
    {
        $this->verifyType($entity);

        return $this->stateMachine->can($entity, $action);
    }

    public function apply($entity, string $action): void
    {
        $this->verifyType($entity);
        $this->stateMachine->apply($entity, $action);
    }

    public function getInitialStatus(): string
    {
        if (!isset($this->stateMachine->getDefinition()->getInitialPlaces()[0])) {
            throw new LogicException("No initial place is defined");
        }

        return $this->stateMachine->getDefinition()->getInitialPlaces()[0];
    }

    private function verifyType($entity): void
    {
        if (get_class($entity) === $this->type) {
            return;
        }

        throw new LogicException('The entity must be an instance of ' . $this->type);
    }
}
