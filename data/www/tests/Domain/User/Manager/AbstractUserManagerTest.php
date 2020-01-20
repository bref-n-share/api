<?php

namespace App\Tests\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Manager\StructureManagerChain;
use App\Domain\User\DTO\UserEdit;
use App\Domain\User\Encoder\PasswordEncoderInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Manager\AbstractUserManager;
use App\Domain\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbstractUserManagerTest extends TestCase
{
    protected const ARCHIVE_ACTION = 'archive';

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $passwordEncoder;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $userRepository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $structureManagerChain;

    /** @var AbstractUserManager */
    private $abstractUserManager;

    protected function setUp()
    {
        $this->passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->structureManagerChain = $this->createMock(StructureManagerChain::class);

        $this->abstractUserManager = $this->getMockForAbstractClass(
            AbstractUserManager::class,
            [
                $this->passwordEncoder,
                $this->workflowProcessor,
                $this->userRepository,
                $this->structureManagerChain
            ]
        );
    }

    public function testEncodePassword(): void
    {
        $user = $this->createMock(User::class);
        $password = 'password';

        $this->passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($user, $password)
            ->willReturn('encodedPassword')
        ;

        $this->assertEquals('encodedPassword', $this->abstractUserManager->encodePassword($user, $password));
    }


    public function testRetrieve(): void
    {
        $id = 'id';
        $user = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($user)
        ;

        $this->assertEquals($user, $this->abstractUserManager->retrieve($id));
    }

    public function testRetrieveNotFound(): void
    {
        $id = 'id';

        $this->userRepository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willThrowException(new NotFoundHttpException())
        ;

        $this->expectException(NotFoundHttpException::class);

        $this->abstractUserManager->retrieve($id);
    }


    public function testArchiveCannotBeArchived(): void
    {
        $id = 'id';
        $user = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($user)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->with($user, self::ARCHIVE_ACTION)
            ->willReturn(false)
        ;

        $this->expectException(ConflictException::class);

        $this->abstractUserManager->archive($id);
    }

    public function testArchive(): void
    {
        $id = 'id';
        $user = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($user)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->with($user, self::ARCHIVE_ACTION)
            ->willReturn(true)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('apply')
            ->with($user, self::ARCHIVE_ACTION)
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user)
            ->willReturn($user)
        ;

        $this->abstractUserManager->archive($id);
    }


    public function testGetUpdatedEntityFullDto(): void
    {
        $id = 'id';
        $entity = $this->createMock(User::class);
        $dto = $this->createMock(UserEdit::class);

        $this->userRepository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($entity)
        ;

        $dto
            ->expects($this->once())
            ->method('getFirstName')
            ->willReturn('firstName')
        ;

        $dto
            ->expects($this->once())
            ->method('getLastName')
            ->willReturn('lastName')
        ;

        $entity
            ->expects($this->once())
            ->method('setFirstName')
            ->with('firstName')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setLastName')
            ->with('lastName')
            ->willReturn($entity)
        ;

        $this->abstractUserManager->getUpdatedEntity($dto, $id);
    }

    public function testGetUpdatedEntityPartDto(): void
    {
        $id = 'id';
        $entity = $this->createMock(User::class);
        $dto = $this->createMock(UserEdit::class);

        $this->userRepository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($entity)
        ;

        $dto
            ->expects($this->once())
            ->method('getFirstName')
            ->willReturn('firstName')
        ;

        $dto
            ->expects($this->once())
            ->method('getLastName')
            ->willReturn(null)
        ;

        $entity
            ->expects($this->once())
            ->method('getLastName')
            ->willReturn('lastName')
        ;

        $entity
            ->expects($this->once())
            ->method('setFirstName')
            ->with('firstName')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setLastName')
            ->with('lastName')
            ->willReturn($entity)
        ;

        $this->abstractUserManager->getUpdatedEntity($dto, $id);
    }
}
