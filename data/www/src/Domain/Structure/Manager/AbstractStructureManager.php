<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Structure\Entity\Structure;
use App\Domain\Structure\Repository\StructureRepositoryInterface;
use App\Domain\Structure\Workflow\StructureWorkflowProcessorInterface;

abstract class AbstractStructureManager implements StructureManagerInterface
{
    protected const ARCHIVE_ACTION = 'archive';

    private StructureRepositoryInterface $repository;

    private StructureWorkflowProcessorInterface $workflowProcessor;

    public function __construct(
        StructureWorkflowProcessorInterface $workflowProcessor,
        StructureRepositoryInterface $repository
    ) {
        $this->workflowProcessor = $workflowProcessor;
        $this->repository = $repository;
    }

    public function retrieve(string $id): Structure
    {
        return $this->repository->retrieve($id);
    }

    /**
     * @return Structure[]
     */
    public function retrieveAll(): array
    {
        return $this->repository->retrieveAll();
    }

    public function save(Structure $structure): Structure
    {
        return $this->repository->save($structure);
    }

    public function archive(string $id): void
    {
        $entity = $this->retrieve($id);
        if ($this->workflowProcessor->can($entity, self::ARCHIVE_ACTION)) {
            $this->workflowProcessor->apply($entity, self::ARCHIVE_ACTION);
            $this->repository->save($entity);

            return;
        }

        throw new ConflictException('This structure can\'t be archive at this moment');
    }

    public function getInitialStatus(): string
    {
        return $this->workflowProcessor->getInitialStatus();
    }
}
