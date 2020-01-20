<?php

namespace App\Domain\Core\Notification;

use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;

interface CustomNotificationProcessorInterface
{
    public function supports(string $channel): bool;

    public function handle(CustomSocialNetworkNotificationDto $notification): bool;
}
