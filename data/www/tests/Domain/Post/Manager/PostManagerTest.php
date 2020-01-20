<?php

namespace App\Tests\Domain\Post\Manager;

use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Manager\PostManager;
use App\Domain\Post\Notification\PostNotificationProcessorChain;
use App\Domain\Post\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class PostManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $processorChain;

    /** @var PostManager */
    private $postManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(PostRepositoryInterface::class);
        $this->processorChain = $this->createMock(PostNotificationProcessorChain::class);

        $this->postManager = new PostManager($this->workflowProcessor, $this->repository, $this->processorChain);
    }

    public function testCreate(): void
    {
        $this->expectException(\LogicException::class);

        $this->postManager->create($this->createMock(Post::class));
    }

    public function testPublish(): void
    {
        $post = $this->createMock(Post::class);
        $firstChannel = 'firstChannel';
        $secondChannel = 'secondChannel';

        $this->processorChain
            ->expects($this->exactly(2))
            ->method('handle')
            ->withConsecutive(
                [$post, $firstChannel],
                [$post, $secondChannel]
            )
            ->willReturn(true)
        ;

        $post
            ->expects($this->exactly(2))
            ->method('addChannel')
            ->withConsecutive([$firstChannel], [$secondChannel])
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($post)
        ;

        $this->postManager->publish($post, [$firstChannel, $secondChannel]);
    }
}
