<?php

namespace App\Tests\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Post\DTO\RequestEdit;
use App\Domain\Post\Entity\Information;
use App\Domain\Post\Entity\Request;
use App\Domain\Post\Manager\RequestManager;
use App\Domain\Post\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RequestManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var RequestManager */
    private $requestManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(PostRepositoryInterface::class);

        $this->requestManager = new RequestManager($this->workflowProcessor, $this->repository);
    }

    public function testCreateNotInformationEntity(): void
    {
        $post = $this->createMock(Information::class);

        $this->expectException(ConflictException::class);

        $this->requestManager->create($post);
    }

    public function testCreate(): void
    {
        $post = $this->createMock(Request::class);
        $status = 'DRAFT';

        $this->workflowProcessor
            ->expects($this->once())
            ->method('getInitialStatus')
            ->willReturn($status)
        ;

        $post
            ->expects($this->once())
            ->method('setStatus')
            ->with($status)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($post)
            ->willReturn($post)
        ;

        $this->assertEquals($post, $this->requestManager->create($post));
    }

    public function testGetUpdatedEntityNotRequestEntity(): void
    {
        $postEdit = $this->createMock(RequestEdit::class);
        $post = $this->createMock(Information::class);

        $this->expectException(ConflictException::class);

        $this->requestManager->getUpdatedEntity($postEdit, $post);
    }

    public function testParticipate(): void
    {
        $id = 'id';
        $request = $this->createMock(Request::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($request)
        ;

        $request
            ->expects($this->once())
            ->method('participate')
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($request)
        ;

        $this->assertEquals($request, $this->requestManager->participate($id));
    }
}
