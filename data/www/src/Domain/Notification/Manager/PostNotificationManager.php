<?php

namespace App\Domain\Notification\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Notification\DTO\PostNotificationCreate;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Repository\NotificationRepositoryInterface;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;

class PostNotificationManager extends NotificationManager
{
    private PostRepositoryInterface $postRepository;

    public function __construct(
        WorkflowProcessorInterface $workflowProcessor,
        NotificationRepositoryInterface $repository,
        NotificationFactory $notificationFactory,
        PostRepositoryInterface $postRepository
    ) {
        parent::__construct($workflowProcessor, $repository, $notificationFactory);
        $this->postRepository = $postRepository;
    }

    public function create(Post $post, PostNotificationCreate $postNotificationDto): Notification
    {
        if (in_array(self::APP_MOB, $post->getChannels())) {
            throw new ConflictException('Ce post a déjà été publié');
        }

        $notification = $this->notificationFactory->createPostNotification(
            $post,
            $postNotificationDto->getExpirationDate()
        );
        $notification->setStatus($this->workflowProcessor->getInitialStatus());

        $this->repository->save($notification);

        $post->addChannel(self::APP_MOB);

        $this->postRepository->save($post);

        return $notification;
    }
}
