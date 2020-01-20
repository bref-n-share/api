<?php

namespace App\Domain\Post\Manager;

use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Notification\PostNotificationProcessorChain;
use App\Domain\Post\Repository\PostRepositoryInterface;

class PostManager extends AbstractPostManager
{
    private PostNotificationProcessorChain $processorChain;

    public function __construct(
        WorkflowProcessorInterface $workflowProcessor,
        PostRepositoryInterface $repository,
        PostNotificationProcessorChain $processorChain
    ) {
        parent::__construct($workflowProcessor, $repository);
        $this->processorChain = $processorChain;
    }

    public function create(Post $post): Post
    {
        throw new \LogicException('Must not be called');
    }

    public function publish(Post $post, array $channels): void
    {
        foreach ($channels as $channel) {
            if ($this->processorChain->handle($post, $channel)) {
                $post->addChannel($channel);
            }
        }

        $this->save($post);
    }
}
