<?php

namespace App\Domain\Notification\Manager;

use App\Domain\Notification\DTO\SimpleNotificationCreate;
use App\Domain\Notification\Entity\Notification;

class SimpleNotificationManager extends AbstractNotificationManager
{
    public function create(SimpleNotificationCreate $simpleNotificationCreate): Notification
    {
        $notification = $this->notificationFactory->createSimpleNotification($simpleNotificationCreate);
        $notification->setStatus($this->workflowProcessor->getInitialStatus());

        $this->repository->save($notification);

        return $notification;
    }
}
