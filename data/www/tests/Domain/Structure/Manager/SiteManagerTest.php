<?php

namespace App\Tests\Domain\Structure\Manager;

use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\DTO\SiteEdit;
use App\Domain\Structure\Entity\Organization;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Manager\SiteManager;
use App\Domain\Structure\Repository\StructureRepositoryInterface;
use PHPUnit\Framework\TestCase;

class SiteManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var SiteManager */
    private $siteManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(StructureRepositoryInterface::class);

        $this->siteManager = new SiteManager($this->workflowProcessor, $this->repository);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->siteManager->supports($this->createMock(Site::class)));
        $this->assertFalse($this->siteManager->supports($this->createMock(Organization::class)));
    }


    public function testGetUpdatedEntityFullDto(): void
    {
        $entity = $this->createMock(Site::class);
        $dto = $this->createMock(SiteEdit::class);

        $dto
            ->expects($this->once())
            ->method('getAddress')
            ->willReturn('address')
        ;

        $dto
            ->expects($this->once())
            ->method('getPostalCode')
            ->willReturn('postalCode')
        ;

        $dto
            ->expects($this->once())
            ->method('getCity')
            ->willReturn('city')
        ;

        $dto
            ->expects($this->once())
            ->method('getPhone')
            ->willReturn('phone')
        ;

        $dto
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('description')
        ;

        $dto
            ->expects($this->once())
            ->method('getLongitude')
            ->willReturn('longitude')
        ;

        $dto
            ->expects($this->once())
            ->method('getLatitude')
            ->willReturn('latitude')
        ;

        $entity
            ->expects($this->once())
            ->method('setAddress')
            ->with('address')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setPostalCode')
            ->with('postalCode')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setCity')
            ->with('city')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setPhone')
            ->with('phone')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setDescription')
            ->with('description')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setLongitude')
            ->with('longitude')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setLatitude')
            ->with('latitude')
            ->willReturn($entity)
        ;

        $this->siteManager->getUpdatedEntity($dto, $entity);
    }

    public function testGetUpdatedEntityPartDto(): void
    {
        $entity = $this->createMock(Site::class);
        $dto = $this->createMock(SiteEdit::class);

        $dto
            ->expects($this->once())
            ->method('getAddress')
            ->willReturn('address')
        ;

        $dto
            ->expects($this->once())
            ->method('getPostalCode')
            ->willReturn('postalCode')
        ;

        $dto
            ->expects($this->once())
            ->method('getCity')
            ->willReturn('city')
        ;

        $dto
            ->expects($this->once())
            ->method('getPhone')
            ->willReturn('phone')
        ;

        $dto
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('description')
        ;

        $dto
            ->expects($this->once())
            ->method('getLongitude')
            ->willReturn(null)
        ;

        $dto
            ->expects($this->once())
            ->method('getLatitude')
            ->willReturn(null)
        ;

        $entity
            ->expects($this->once())
            ->method('getLongitude')
            ->willReturn('longitude')
        ;

        $entity
            ->expects($this->once())
            ->method('getLatitude')
            ->willReturn('latitude')
        ;

        $entity
            ->expects($this->once())
            ->method('setAddress')
            ->with('address')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setPostalCode')
            ->with('postalCode')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setCity')
            ->with('city')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setPhone')
            ->with('phone')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setDescription')
            ->with('description')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setLongitude')
            ->with('longitude')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setLatitude')
            ->with('latitude')
            ->willReturn($entity)
        ;

        $this->siteManager->getUpdatedEntity($dto, $entity);
    }
}
