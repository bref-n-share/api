<?php

namespace App\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Entity\Site;

abstract class AbstractPostManager implements PostManagerInterface
{
    protected const ARCHIVE_ACTION = 'archive';

    protected WorkflowProcessorInterface $workflowProcessor;

    protected PostRepositoryInterface $repository;

    public function __construct(
        WorkflowProcessorInterface $workflowProcessor,
        PostRepositoryInterface $repository
    ) {
        $this->workflowProcessor = $workflowProcessor;
        $this->repository = $repository;
    }

    public function retrieve(string $id): Post
    {
        return $this->repository->retrieve($id);
    }

    /**
     * @return Post[]
     */
    public function retrieveAll(): array
    {
        return $this->repository->retrieveAll();
    }

    public function save(Post $post): Post
    {
        return $this->repository->save($post);
    }

    public function archive(string $id): void
    {
        $entity = $this->retrieve($id);
        if ($this->workflowProcessor->can($entity, self::ARCHIVE_ACTION)) {
            $this->workflowProcessor->apply($entity, self::ARCHIVE_ACTION);
            $this->repository->save($entity);

            return;
        }

        throw new ConflictException('Le post ne peut pas être archivé');
    }

    /**
     * @param Site[] $sites
     *
     * @return Post[]
     */
    public function retrieveAllBySites(array $sites): array
    {
        $posts = [];

        foreach ($sites as $site) {
            $posts = array_merge($posts, $this->repository->retrieveAllBySite($site->getId()->toString()));
        }

        return $posts;
    }
}
