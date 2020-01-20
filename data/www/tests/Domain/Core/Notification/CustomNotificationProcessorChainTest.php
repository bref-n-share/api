<?php

namespace App\Tests\Domain\Core\Notification;

use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;
use App\Domain\Core\Notification\CustomNotificationProcessorChain;
use App\Domain\Core\Notification\CustomNotificationProcessorInterface;
use PHPUnit\Framework\TestCase;

class CustomNotificationProcessorChainTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $firstProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $secondProcessor;

    /** @var CustomNotificationProcessorChain */
    private $processorChain;

    protected function setUp()
    {
        $this->firstProcessor = $this->createMock(CustomNotificationProcessorInterface::class);
        $this->secondProcessor = $this->createMock(CustomNotificationProcessorInterface::class);

        $this->processorChain = new CustomNotificationProcessorChain([$this->firstProcessor, $this->secondProcessor]);
    }

    public function testHandleNoProcessorCanHandle(): void
    {
        $notification = $this->createMock(CustomSocialNetworkNotificationDto::class);
        $channel = 'channel';

        $this->firstProcessor
            ->expects($this->once())
            ->method('supports')
            ->with($channel)
            ->willReturn(false)
        ;

        $this->secondProcessor
            ->expects($this->once())
            ->method('supports')
            ->with($channel)
            ->willReturn(false)
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processorChain->handle($notification, $channel);
    }

    public function testHandle(): void
    {
        $notification = $this->createMock(CustomSocialNetworkNotificationDto::class);
        $channel = 'channel';

        $this->firstProcessor
            ->expects($this->once())
            ->method('supports')
            ->with($channel)
            ->willReturn(true)
        ;

        $this->firstProcessor
            ->expects($this->once())
            ->method('handle')
            ->with($notification)
            ->willReturn(true)
        ;

        $this->assertTrue($this->processorChain->handle($notification, $channel));
    }
}
