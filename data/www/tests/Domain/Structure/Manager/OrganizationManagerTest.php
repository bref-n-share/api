<?php

namespace App\Tests\Domain\Structure\Manager;

use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Entity\Organization;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Manager\OrganizationManager;
use App\Domain\Structure\Repository\StructureRepositoryInterface;
use PHPUnit\Framework\TestCase;

class OrganizationManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var OrganizationManager */
    private $organizationManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(StructureRepositoryInterface::class);

        $this->organizationManager = new OrganizationManager($this->workflowProcessor, $this->repository);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->organizationManager->supports($this->createMock(Organization::class)));
        $this->assertFalse($this->organizationManager->supports($this->createMock(Site::class)));
    }
}
