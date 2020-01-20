<?php

namespace App\Infrastructure\Notification;

use App\Domain\Post\Entity\Post;
use App\Domain\Post\Notification\PostNotificationProcessorInterface;
use MartinGeorgiev\SocialPost\Message;
use MartinGeorgiev\SocialPost\Publisher;

class SocialNetworkPostNotificationProcessor implements PostNotificationProcessorInterface
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

    public function handle(Post $post): bool
    {
        $message = new Message($post->getDescription() . " - " . $post->getSite()->getName());
        $message->setNetworksToPublishOn([$this->type]);

        return $this->publisher->publish($message);
    }
}
