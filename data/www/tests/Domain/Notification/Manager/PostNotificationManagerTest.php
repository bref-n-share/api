<?php

namespace App\Tests\Domain\Core\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Notification\DTO\PostNotificationCreate;
use App\Domain\Notification\Entity\PostNotification;
use App\Domain\Notification\Manager\NotificationFactory;
use App\Domain\Notification\Manager\PostNotificationManager;
use App\Domain\Notification\Repository\NotificationRepositoryInterface;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class PostNotificationManagerTest extends TestCase
{
    protected const APP_MOB = 'app_mob';

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $repository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $notificationFactory;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $postRepository;

    /** @var PostNotificationManager */
    private $manager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(NotificationRepositoryInterface::class);
        $this->notificationFactory = $this->createMock(NotificationFactory::class);
        $this->postRepository = $this->createMock(PostRepositoryInterface::class);

        $this->manager = new PostNotificationManager(
            $this->workflowProcessor,
            $this->repository,
            $this->notificationFactory,
            $this->postRepository
        );
    }

    public function testCreateAlreadyNotified(): void
    {
        $post = $this->createMock(Post::class);

        $post
            ->expects($this->once())
            ->method('getChannels')
            ->willReturn([self::APP_MOB])
        ;

        $this->expectException(ConflictException::class);

        $this->manager->create($post, $this->createMock(PostNotificationCreate::class));
    }

    public function testCreate(): void
    {
        $post = $this->createMock(Post::class);
        $postNotificationDto = $this->createMock(PostNotificationCreate::class);
        $notification = $this->createMock(PostNotification::class);

        $post
            ->expects($this->once())
            ->method('getChannels')
            ->willReturn([])
        ;

        $postNotificationDto
            ->expects($this->once())
            ->method('getExpirationDate')
        ;

        $this->notificationFactory
            ->expects($this->once())
            ->method('createPostNotification')
            ->willReturn($notification)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('getInitialStatus')
            ->willReturn('VALID')
        ;

        $notification
            ->expects($this->once())
            ->method('setStatus')
            ->with('VALID')
            ->willReturn($notification)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($notification)
        ;

        $post
            ->expects($this->once())
            ->method('addChannel')
            ->with(self::APP_MOB)
        ;

        $this->postRepository
            ->expects($this->once())
            ->method('save')
            ->with($post)
        ;

        $this->assertEquals($notification, $this->manager->create($post, $postNotificationDto));
    }
}
