<?php

namespace App\Tests\Domain\Core\Manager;

use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Notification\DTO\SimpleNotificationCreate;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Entity\SimpleNotification;
use App\Domain\Notification\Manager\NotificationFactory;
use App\Domain\Notification\Manager\SimpleNotificationManager;
use App\Domain\Notification\Repository\NotificationRepositoryInterface;
use PHPUnit\Framework\TestCase;

class SimpleNotificationManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $repository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $notificationFactory;

    /** @var SimpleNotificationManager */
    private $manager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(NotificationRepositoryInterface::class);
        $this->notificationFactory = $this->createMock(NotificationFactory::class);

        $this->manager = new SimpleNotificationManager(
            $this->workflowProcessor,
            $this->repository,
            $this->notificationFactory
        );
    }

    public function testCreate(): void
    {
        $notification = $this->createMock(SimpleNotification::class);
        $simpleNotificationCreate = $this->createMock(SimpleNotificationCreate::class);

        $this->notificationFactory
            ->expects($this->once())
            ->method('createSimpleNotification')
            ->with($simpleNotificationCreate)
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
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($notification)
        ;

        $this->assertEquals($notification, $this->manager->create($simpleNotificationCreate));
    }

}
