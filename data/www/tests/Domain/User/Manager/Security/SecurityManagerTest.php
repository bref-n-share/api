<?php

namespace App\Tests\Domain\User\Manager\Security;

use App\Domain\User\DTO\AuthenticateUserDTO;
use App\Domain\User\Entity\User;
use App\Domain\User\Manager\Security\SecurityManager;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Encoder\PasswordEncoder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SecurityManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $userRepository;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $passwordEncoder;

    /** @var SecurityManager */
    private $securityManager;

    public function setUp()
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordEncoder = $this->createMock(PasswordEncoder::class);

        $this->securityManager = new SecurityManager($this->userRepository, $this->passwordEncoder);
    }

    public function testGetUserTokenNotFound(): void
    {
        $dto = $this->getMockBuilder(AuthenticateUserDTO::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dto->email = 'email';

        $this->userRepository
            ->expects($this->once())
            ->method('retrieveOneBy')
            ->willThrowException(new NotFoundHttpException())
        ;

        $this->expectException(AccessDeniedHttpException::class);

        $this->securityManager->getUserToken($dto);
    }

    public function testGetUserTokenPasswordInvalid(): void
    {
        $user = $this->createMock(User::class);

        $dto = $this->getMockBuilder(AuthenticateUserDTO::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dto->email = 'email';
        $dto->password = 'password';

        $this->userRepository
            ->expects($this->once())
            ->method('retrieveOneBy')
            ->willReturn($user)
        ;

        $this->passwordEncoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, 'password')
            ->willReturn(false)
        ;

        $this->expectException(AccessDeniedHttpException::class);

        $this->securityManager->getUserToken($dto);
    }

    public function testGetUserTokenUserIdArchived(): void
    {
        $user = $this->createMock(User::class);

        $dto = $this->getMockBuilder(AuthenticateUserDTO::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dto->email = 'email';
        $dto->password = 'password';

        $this->userRepository
            ->expects($this->once())
            ->method('retrieveOneBy')
            ->willReturn($user)
        ;

        $this->passwordEncoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, 'password')
            ->willReturn(true)
        ;

        $user
            ->expects($this->once())
            ->method('isArchived')
            ->willReturn(true)
        ;

        $this->expectException(AccessDeniedHttpException::class);

        $this->securityManager->getUserToken($dto);
    }

    public function testGetUserToken(): void
    {
        $uuidInterface = $this->createMock(UuidInterface::class);
        $user = $this->createMock(User::class);

        $dto = $this->getMockBuilder(AuthenticateUserDTO::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dto->email = 'email';
        $dto->password = 'password';

        $this->userRepository
            ->expects($this->once())
            ->method('retrieveOneBy')
            ->willReturn($user)
        ;

        $this->passwordEncoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, 'password')
            ->willReturn(true)
        ;

        $user
            ->expects($this->once())
            ->method('isArchived')
            ->willReturn(false)
        ;

        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn($uuidInterface)
        ;

        $uuidInterface
            ->expects($this->once())
            ->method('toString')
            ->willReturn('uuid')
        ;

        $this->assertEquals('uuid', $this->securityManager->getUserToken($dto));
    }
}
