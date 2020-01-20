<?php

namespace App\Tests\Domain\Post\Notification;

use App\Domain\Post\Entity\Post;
use App\Domain\Post\Notification\PostNotificationProcessorChain;
use App\Domain\Post\Notification\PostNotificationProcessorInterface;
use PHPUnit\Framework\TestCase;

class PostNotificationProcessorChainTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $firstProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $secondProcessor;

    /** @var PostNotificationProcessorChain */
    private $processorChain;

    protected function setUp()
    {
        $this->firstProcessor = $this->createMock(PostNotificationProcessorInterface::class);
        $this->secondProcessor = $this->createMock(PostNotificationProcessorInterface::class);

        $this->processorChain = new PostNotificationProcessorChain([$this->firstProcessor, $this->secondProcessor]);
    }

    public function testHandleNoProcessorCanHandle(): void
    {
        $post = $this->createMock(Post::class);
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

        $this->processorChain->handle($post, $channel);
    }

    public function testHandle(): void
    {
        $post = $this->createMock(Post::class);
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
            ->with($post)
            ->willReturn(true)
        ;

        $this->assertTrue($this->processorChain->handle($post, $channel));
    }
}
