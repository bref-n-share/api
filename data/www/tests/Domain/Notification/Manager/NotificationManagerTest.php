<?php

namespace App\Tests\Domain\Core\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Manager\NotificationFactory;
use App\Domain\Notification\Manager\NotificationManager;
use App\Domain\Notification\Repository\NotificationRepositoryInterface;
use App\Domain\Structure\Entity\Site;
use PHPUnit\Framework\TestCase;

class NotificationManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $repository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $notificationFactory;

    /** @var NotificationManager */
    private $manager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(NotificationRepositoryInterface::class);
        $this->notificationFactory = $this->createMock(NotificationFactory::class);

        $this->manager = new NotificationManager(
            $this->workflowProcessor,
            $this->repository,
            $this->notificationFactory
        );
    }

    public function testHasExpiredAlreadyExpired(): void
    {
        $notification = $this->createMock(Notification::class);

        $notification
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn('EXPIRED')
        ;

        $this->assertTrue($this->manager->hasExpired($notification));
    }

    public function testHasExpiredUpdateExpiredError(): void
    {
        $expiredDate = (new \DateTime())->sub(new \DateInterval('P2D'));
        $notification = $this->createMock(Notification::class);

        $notification
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn('VALID')
        ;

        $notification
            ->expects($this->once())
            ->method('getExpirationDate')
            ->willReturn($expiredDate)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->willReturn(false)
        ;

        $this->expectException(ConflictException::class);

        $this->assertTrue($this->manager->hasExpired($notification));
    }

    public function testHasExpiredUpdateExpired(): void
    {
        $expiredDate = (new \DateTime())->sub(new \DateInterval('P2D'));
        $notification = $this->createMock(Notification::class);

        $notification
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn('VALID')
        ;

        $notification
            ->expects($this->once())
            ->method('getExpirationDate')
            ->willReturn($expiredDate)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->willReturn(true)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('apply')
            ->with($notification, 'expire')
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($notification)
        ;

        $this->assertTrue($this->manager->hasExpired($notification));
    }

    public function testHasExpiredNotExpired(): void
    {
        $notExpiredDate = (new \DateTime())->add(new \DateInterval('P2D'));
        $notification = $this->createMock(Notification::class);

        $notification
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn('VALID')
        ;

        $notification
            ->expects($this->once())
            ->method('getExpirationDate')
            ->willReturn($notExpiredDate)
        ;

        $this->assertFalse($this->manager->hasExpired($notification));
    }

    public function testGetValidNotifications(): void
    {
        $firstSite = $this->createMock(Site::class);
        $secondSite = $this->createMock(Site::class);

        $firstValidNotification = $this->createMock(Notification::class);
        $secondNotification = $this->createMock(Notification::class);

        $firstSite
            ->expects($this->once())
            ->method('getValidNotifications')
            ->willReturn([$firstValidNotification])
        ;

        $secondSite
            ->expects($this->once())
            ->method('getValidNotifications')
            ->willReturn([$secondNotification])
        ;

        $this->assertEquals(
            [$firstValidNotification, $secondNotification],
            $this->manager->getValidNotifications([$firstSite, $secondSite])
        );
    }
}
