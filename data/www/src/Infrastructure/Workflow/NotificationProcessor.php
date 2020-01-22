<?php

namespace App\Infrastructure\Workflow;

use App\Domain\Notification\Entity\Notification;
use Symfony\Component\Workflow\Exception\LogicException;

class NotificationProcessor extends WorkflowProcessor
{
    protected function verifyType($entity): void
    {
        if ($entity instanceof Notification) {
            return;
        }

        throw new LogicException('The entity must be an instance of ' . $this->type);
    }
}
