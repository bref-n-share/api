<?php

namespace App\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;
use App\Domain\Post\Workflow\PostWorkflowProcessorInterface;

abstract class AbstractPostManager implements PostManagerInterface
{
    protected const ARCHIVE_ACTION = 'archive';

    protected PostWorkflowProcessorInterface $workflowProcessor;

    protected PostRepositoryInterface $repository;

    public function __construct(
        PostWorkflowProcessorInterface $workflowProcessor,
        PostRepositoryInterface $repository
    ) {
        $this->workflowProcessor = $workflowProcessor;
        $this->repository = $repository;
    }

    public function retrieve(string $id): Post
    {
        return $this->repository->retrieve($id);
    }

    public function archive(string $id): void
    {
        $entity = $this->retrieve($id);
        if ($this->workflowProcessor->can($entity, self::ARCHIVE_ACTION)) {
            $this->workflowProcessor->apply($entity, self::ARCHIVE_ACTION);
            $this->repository->save($entity);

            return;
        }

        throw new ConflictException('This post can\'t be archive at this moment');
    }
}
