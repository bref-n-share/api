<?php

namespace App\Tests\Domain\Post\Notification;

use App\Domain\Post\Entity\Post;
use App\Domain\Post\Publication\PostPublicationProcessorChain;
use App\Domain\Post\Publication\PostPublicationProcessorInterface;
use PHPUnit\Framework\TestCase;

class PostPublicationProcessorChainTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $firstProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $secondProcessor;

    /** @var PostPublicationProcessorChain */
    private $processorChain;

    protected function setUp()
    {
        $this->firstProcessor = $this->createMock(PostPublicationProcessorInterface::class);
        $this->secondProcessor = $this->createMock(PostPublicationProcessorInterface::class);

        $this->processorChain = new PostPublicationProcessorChain([$this->firstProcessor, $this->secondProcessor]);
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
