<?php

namespace App\Domain\Post\Notification;

use App\Domain\Post\Entity\Post;

class NotificationProcessorChain
{
    /**
     * @var NotificationProcessorInterface[]
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

    public function handle(Post $post, string $channel): bool
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($channel)) {
                return $processor->handle($post);
            }
        }

        throw new \InvalidArgumentException('No processor can handle this channel (' . $channel . ')');
    }
}
