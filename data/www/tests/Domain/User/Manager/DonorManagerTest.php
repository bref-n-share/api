<?php

namespace App\Tests\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Manager\StructureManagerChain;
use App\Domain\User\Encoder\PasswordEncoderInterface;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\Member;
use App\Domain\User\Manager\DonorManager;
use App\Domain\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DonorManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $passwordEncoder;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $userRepository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $structureManagerChain;

    /** @var DonorManager */
    private $donorManager;

    protected function setUp()
    {
        $this->passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->structureManagerChain = $this->createMock(StructureManagerChain::class);

        $this->donorManager = new DonorManager(
            $this->passwordEncoder,
            $this->workflowProcessor,
            $this->userRepository,
            $this->structureManagerChain
        );
    }


    public function testCreateNotInformationEntity(): void
    {
        $user = $this->createMock(Member::class);

        $this->expectException(ConflictException::class);

        $this->donorManager->create($user);
    }

    public function testCreate(): void
    {
        $user = $this->createMock(Donor::class);
        $status = 'DRAFT';

        $this->workflowProcessor
            ->expects($this->once())
            ->method('getInitialStatus')
            ->willReturn($status)
        ;

        $user
            ->expects($this->once())
            ->method('addRole')
            ->with('ROLE_USER')
            ->willReturn($user)
        ;

        $user
            ->expects($this->once())
            ->method('setStatus')
            ->with($status)
            ->willReturn($user)
        ;

        $user
            ->expects($this->once())
            ->method('getPassword')
            ->willReturn('pass')
        ;

        $this->passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->willReturn('encodedPassword')
        ;

        $user
            ->expects($this->once())
            ->method('setPassword')
            ->with('encodedPassword')
            ->willReturn($user)
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user)
            ->willReturn($user)
        ;

        $this->assertEquals($user, $this->donorManager->create($user));
    }

    public function testAddFavorite(): void
    {
        $donor = $this->createMock(Donor::class);
        $site = $this->createMock(Site::class);

        $donor
            ->expects($this->once())
            ->method('addSite')
            ->with($site)
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($donor)
        ;

        $this->donorManager->addFavorite($donor, $site);
    }

    public function testRemoveFavorite(): void
    {
        $donor = $this->createMock(Donor::class);
        $site = $this->createMock(Site::class);

        $donor
            ->expects($this->once())
            ->method('removeSite')
            ->with($site)
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($donor)
        ;

        $this->donorManager->removeFavorite($donor, $site);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->donorManager->supports($this->createMock(Donor::class)));
        $this->assertFalse($this->donorManager->supports($this->createMock(Member::class)));
    }
}
