<?php

namespace App\Domain\Core\Notification;

use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;

class CustomNotificationProcessorChain
{
    /**
     * @var CustomNotificationProcessorInterface[]
     */
    private iterable $processors;

    /**
     * NotificatorProcessorChain constructor.
     *
     * @param iterable $processors
     */
    public function __construct(iterable $processors)
    {
        $this->processors = $processors;
    }

    public function handle(CustomSocialNetworkNotificationDto $notification, string $channel): bool
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($channel)) {
                return $processor->handle($notification);
            }
        }

        throw new \InvalidArgumentException('No processor can handle this channel (' . $channel . ')');
    }
}
