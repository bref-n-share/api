<?php

namespace App\Infrastructure\Notification;

use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;
use App\Domain\Core\Notification\CustomNotificationProcessorInterface;
use MartinGeorgiev\SocialPost\Message;
use MartinGeorgiev\SocialPost\Publisher;

class SocialNetworkCustomNotificationProcessor implements CustomNotificationProcessorInterface
{
    private string $type;

    private Publisher $publisher;

    public function __construct(string $type, Publisher $publisher)
    {
        $this->type = $type;
        $this->publisher = $publisher;
    }

    public function supports(string $channel): bool
    {
        return strtolower($channel) === strtolower($this->type);
    }

    public function handle(CustomSocialNetworkNotificationDto $notification): bool
    {
        $message = new Message($notification->getMessage() . " - " . $notification->getStructure()->getName());
        $message->setNetworksToPublishOn([$this->type]);

        return $this->publisher->publish($message);
    }
}
