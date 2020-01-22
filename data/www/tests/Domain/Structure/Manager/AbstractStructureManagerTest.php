<?php

namespace App\Tests\Domain\Structure\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Entity\Structure;
use App\Domain\Structure\Manager\AbstractStructureManager;
use App\Domain\Structure\Repository\StructureRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbstractStructureManagerTest extends TestCase
{
    protected const ARCHIVE_ACTION = 'archive';

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var AbstractStructureManager */
    private $abstractStructureManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(StructureRepositoryInterface::class);

        $this->abstractStructureManager = $this->getMockForAbstractClass(
            AbstractStructureManager::class,
            [
                $this->workflowProcessor,
                $this->repository
            ]
        );
    }

    public function testRetrieve(): void
    {
        $id = 'id';
        $structure = $this->createMock(Structure::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($structure)
        ;

        $this->assertEquals($structure, $this->abstractStructureManager->retrieve($id));
    }

    public function testRetrieveNotFound(): void
    {
        $id = 'id';

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willThrowException(new NotFoundHttpException())
        ;

        $this->expectException(NotFoundHttpException::class);

        $this->abstractStructureManager->retrieve($id);
    }

    public function testRetrieveAll(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('retrieveAll')
            ->willReturn([])
        ;

        $this->assertEquals([], $this->abstractStructureManager->retrieveAll());
    }

    public function testSave(): void
    {
        $structure = $this->createMock(Structure::class);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($structure)
            ->willReturn($structure)
        ;

        $this->assertEquals($structure, $this->abstractStructureManager->save($structure));
    }

    public function testArchiveCannotBeArchived(): void
    {
        $id = 'id';
        $structure = $this->createMock(Structure::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($structure)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->with($structure, self::ARCHIVE_ACTION)
            ->willReturn(false)
        ;

        $this->expectException(ConflictException::class);

        $this->abstractStructureManager->archive($id);
    }

    public function testArchive(): void
    {
        $id = 'id';
        $structure = $this->createMock(Structure::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($structure)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->with($structure, self::ARCHIVE_ACTION)
            ->willReturn(true)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('apply')
            ->with($structure, self::ARCHIVE_ACTION)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($structure)
            ->willReturn($structure)
        ;

        $this->abstractStructureManager->archive($id);
    }
}
