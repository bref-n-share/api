<?php

namespace App\Tests\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Structure;
use App\Domain\Structure\Manager\StructureManagerChain;
use App\Domain\Structure\Manager\StructureManagerInterface;
use PHPUnit\Framework\TestCase;

class StructureManagerChainTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $firstManager;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $secondManager;

    /** @var StructureManagerChain */
    private $processorChain;

    protected function setUp()
    {
        $this->firstManager = $this->createMock(StructureManagerInterface::class);
        $this->secondManager = $this->createMock(StructureManagerInterface::class);

        $this->processorChain = new StructureManagerChain([$this->firstManager, $this->secondManager]);
    }

    public function testGetManagerNoManagerSupport(): void
    {
        $structure = $this->createMock(Structure::class);

        $this->firstManager
            ->expects($this->once())
            ->method('supports')
            ->with($structure)
            ->willReturn(false)
        ;

        $this->secondManager
            ->expects($this->once())
            ->method('supports')
            ->with($structure)
            ->willReturn(false)
        ;

        $this->expectException(\LogicException::class);

        $this->processorChain->getManager($structure);
    }

    public function testGetManager(): void
    {
        $structure = $this->createMock(Structure::class);

        $this->firstManager
            ->expects($this->once())
            ->method('supports')
            ->with($structure)
            ->willReturn(true)
        ;

        $this->assertEquals($this->firstManager, $this->processorChain->getManager($structure));
    }
}
