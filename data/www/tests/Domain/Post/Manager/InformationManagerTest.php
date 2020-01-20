<?php

namespace App\Tests\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Post\Entity\Information;
use App\Domain\Post\Entity\Request;
use App\Domain\Post\Manager\InformationManager;
use App\Domain\Post\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class InformationManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var InformationManager */
    private $informationManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(PostRepositoryInterface::class);

        $this->informationManager = new InformationManager($this->workflowProcessor, $this->repository);
    }

    public function testCreateNotInformationEntity(): void
    {
        $post = $this->createMock(Request::class);

        $this->expectException(ConflictException::class);

        $this->informationManager->create($post);
    }

    public function testCreate(): void
    {
        $post = $this->createMock(Information::class);
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

        $this->assertEquals($post, $this->informationManager->create($post));
    }
}
