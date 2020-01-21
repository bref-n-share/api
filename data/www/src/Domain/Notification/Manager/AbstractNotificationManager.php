<?php

namespace App\Domain\Notification\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Repository\NotificationRepositoryInterface;

abstract class AbstractNotificationManager
{
    protected const APP_MOB = 'app_mob';

    protected WorkflowProcessorInterface $workflowProcessor;

    protected NotificationRepositoryInterface $repository;

    protected NotificationFactory $notificationFactory;

    public function __construct(
        WorkflowProcessorInterface $workflowProcessor,
        NotificationRepositoryInterface $repository,
        NotificationFactory $notificationFactory
    ) {
        $this->workflowProcessor = $workflowProcessor;
        $this->repository = $repository;
        $this->notificationFactory = $notificationFactory;
    }

    public function hasExpired(Notification $notification): bool
    {
        if ($notification->getStatus() === $this->workflowProcessor->getInitialStatus()) {
            return true;
        }

        if (
            $notification->getExpirationDate() <= (new \DateTimeImmutable())
        ) {
            if (!$this->workflowProcessor->can($notification, 'expire')) {
                throw new ConflictException('Cannot set the notification status to expired');
            }

            $this->workflowProcessor->apply($notification, 'expire');
            $this->repository->save($notification);

            return true;
        }

        return false;
    }
}
