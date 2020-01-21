<?php

namespace App\Tests\Domain\Core\Notification;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;
use App\Domain\Core\Publication\CustomPublicationProcessorChain;
use App\Domain\Core\Publication\CustomPublicationProcessorInterface;
use PHPUnit\Framework\TestCase;

class CustomPublicationProcessorChainTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $firstProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $secondProcessor;

    /** @var CustomPublicationProcessorChain */
    private $processorChain;

    protected function setUp()
    {
        $this->firstProcessor = $this->createMock(CustomPublicationProcessorInterface::class);
        $this->secondProcessor = $this->createMock(CustomPublicationProcessorInterface::class);

        $this->processorChain = new CustomPublicationProcessorChain([$this->firstProcessor, $this->secondProcessor]);
    }

    public function testHandleNoProcessorCanHandle(): void
    {
        $notification = $this->createMock(CustomSocialNetworkPublicationDto::class);
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
        $notification = $this->createMock(CustomSocialNetworkPublicationDto::class);
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
