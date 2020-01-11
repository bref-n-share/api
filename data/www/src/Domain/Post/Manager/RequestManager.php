<?php

namespace App\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Entity\Request;
use App\Domain\Structure\Entity\Site;

class RequestManager extends AbstractPostManager
{
    public function create(Post $post): Post
    {
        if (!($post instanceof Request)) {
            throw new ConflictException('Must be an instance of ' . Request::class);
        }

        $post->setStatus($this->workflowProcessor->getInitialStatus());

        return $this->repository->save($post);
    }

    /**
     * @param Site[] $sites
     *
     * @return Request[]
     */
    public function retrieveAllBySites(array $sites): array
    {
        $requests = [];

        foreach ($sites as $site) {
            $requests = array_merge($requests, $this->repository->retrieveAllBySite($site->getId()->toString()));
        }

        return $requests;
    }
}
