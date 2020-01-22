<?php

namespace App\Domain\Notification\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Repository\NotificationRepositoryInterface;
use App\Domain\Structure\Entity\Site;

class NotificationManager
{
    protected const APP_MOB = 'app_mob';
    protected const EXPIRED_STATUS = 'EXPIRED';

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

    /**
     * Check if the notification has expired and update the status of the notification
     *
     * @param Notification $notification
     *
     * @return bool
     *
     * @throws ConflictException
     */
    public function hasExpired(Notification $notification): bool
    {
        if ($notification->getStatus() === self::EXPIRED_STATUS) {
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

    public function getValidNotifications(array $sites): array
    {
        $notifications = [];

        /** @var Site $site */
        foreach ($sites as $site) {
            $notifications = array_merge($notifications, $site->getValidNotifications());
        }

        return $notifications;
    }

    public function clean(): void
    {
        $notifications = $this->repository->retrieveAll();

        foreach ($notifications as $notification) {
            $this->hasExpired($notification);
        }
    }
}
