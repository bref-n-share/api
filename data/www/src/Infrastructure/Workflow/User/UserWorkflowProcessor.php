<?php

namespace App\Infrastructure\Workflow\User;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\User\Entity\User;
use App\Domain\User\Workflow\UserWorkflowProcessorInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\StateMachine;

class UserWorkflowProcessor implements UserWorkflowProcessorInterface
{
    private StateMachine $stateMachine;

    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    public function can(User $entity, string $action): bool
    {
        return $this->stateMachine->can($entity, $action);
    }

    public function apply(User $entity, string $action): void
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
