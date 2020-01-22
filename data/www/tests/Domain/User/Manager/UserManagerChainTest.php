<?php

namespace App\Tests\Domain\User\Manager;

use App\Domain\User\Entity\User;
use App\Domain\User\Manager\UserManagerChain;
use App\Domain\User\Manager\UserManagerInterface;
use PHPUnit\Framework\TestCase;

class UserManagerChainTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $firstManager;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $secondManager;

    /** @var UserManagerChain */
    private $processorChain;

    protected function setUp()
    {
        $this->firstManager = $this->createMock(UserManagerInterface::class);
        $this->secondManager = $this->createMock(UserManagerInterface::class);

        $this->processorChain = new UserManagerChain([$this->firstManager, $this->secondManager]);
    }

    public function testGetManagerNoManagerSupport(): void
    {
        $user = $this->createMock(User::class);

        $this->firstManager
            ->expects($this->once())
            ->method('supports')
            ->with($user)
            ->willReturn(false)
        ;

        $this->secondManager
            ->expects($this->once())
            ->method('supports')
            ->with($user)
            ->willReturn(false)
        ;

        $this->expectException(\LogicException::class);

        $this->processorChain->getManager($user);
    }

    public function testGetManager(): void
    {
        $user = $this->createMock(User::class);

        $this->firstManager
            ->expects($this->once())
            ->method('supports')
            ->with($user)
            ->willReturn(true)
        ;

        $this->assertEquals($this->firstManager, $this->processorChain->getManager($user));
    }
}
