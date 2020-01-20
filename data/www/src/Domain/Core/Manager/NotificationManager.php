<?php

namespace App\Domain\Core\Manager;

use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;
use App\Domain\Core\Notification\CustomNotificationProcessorChain;

class NotificationManager
{
    private CustomNotificationProcessorChain $notificationProcessorChain;

    public function __construct(CustomNotificationProcessorChain $notificationProcessorChain)
    {
        $this->notificationProcessorChain = $notificationProcessorChain;
    }

    public function publish(
        CustomSocialNetworkNotificationDto $socialNetworkNotificationDto,
        array $channels
    ): CustomSocialNetworkNotificationDto {
        foreach ($channels as $channel) {
            $this->notificationProcessorChain->handle($socialNetworkNotificationDto, $channel);
        }

        return $socialNetworkNotificationDto;
    }
}
