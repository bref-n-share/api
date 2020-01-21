<?php

namespace App\Domain\Notification\Repository;

use App\Domain\Notification\Entity\Notification;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): Notification;
}
