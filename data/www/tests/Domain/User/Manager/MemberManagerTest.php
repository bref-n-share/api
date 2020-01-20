<?php

namespace App\Tests\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Manager\StructureManagerChain;
use App\Domain\Structure\Manager\StructureManagerInterface;
use App\Domain\User\Encoder\PasswordEncoderInterface;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\Member;
use App\Domain\User\Manager\DonorManager;
use App\Domain\User\Manager\MemberManager;
use App\Domain\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class MemberManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $passwordEncoder;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $userRepository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $structureManagerChain;

    /** @var MemberManager */
    private $memberManager;

    protected function setUp()
    {
        $this->passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->structureManagerChain = $this->createMock(StructureManagerChain::class);

        $this->memberManager = new MemberManager(
            $this->passwordEncoder,
            $this->workflowProcessor,
            $this->userRepository,
            $this->structureManagerChain
        );
    }


    public function testCreateNotInformationEntity(): void
    {
        $user = $this->createMock(Donor::class);

        $this->expectException(ConflictException::class);

        $this->memberManager->create($user);
    }

    public function testCreateNewStructure(): void
    {
        $siteManager = $this->createMock(StructureManagerInterface::class);
        $site = $this->createMock(Site::class);
        $user = $this->createMock(Member::class);
        $status = 'DRAFT';

        $this->workflowProcessor
            ->expects($this->once())
            ->method('getInitialStatus')
            ->willReturn($status)
        ;

        $user
            ->expects($this->exactly(2))
            ->method('addRole')
            ->withConsecutive(['ROLE_USER'], ['ROLE_ADMIN'])
            ->willReturn($user)
        ;

        $user
            ->expects($this->once())
            ->method('setStatus')
            ->with($status)
            ->willReturn($user)
        ;

        $user
            ->expects($this->exactly(3))
            ->method('getStructure')
            ->willReturn($site)
        ;

        $this->structureManagerChain
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($siteManager)
        ;

        $siteManager
            ->expects($this->once())
            ->method('getFormattedStructureFromMemberCreation')
            ->with($site)
            ->willReturn($site)
        ;

        $site
            ->expects($this->once())
            ->method('getId')
            ->willReturn(null)
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

        $this->assertEquals($user, $this->memberManager->create($user));
    }

    public function testCreateAttachStructure(): void
    {
        $siteManager = $this->createMock(StructureManagerInterface::class);
        $site = $this->createMock(Site::class);
        $user = $this->createMock(Member::class);
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
            ->expects($this->exactly(3))
            ->method('getStructure')
            ->willReturn($site)
        ;

        $this->structureManagerChain
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($siteManager)
        ;

        $siteManager
            ->expects($this->once())
            ->method('getFormattedStructureFromMemberCreation')
            ->with($site)
            ->willReturn($site)
        ;

        $site
            ->expects($this->once())
            ->method('getId')
            ->willReturn($this->createMock(UuidInterface::class))
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

        $this->assertEquals($user, $this->memberManager->create($user));
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->memberManager->supports($this->createMock(Member::class)));
        $this->assertFalse($this->memberManager->supports($this->createMock(Donor::class)));
    }

}
