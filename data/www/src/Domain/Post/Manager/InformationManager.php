<?php

namespace App\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Post\Entity\Information;
use App\Domain\Post\Entity\Post;

class InformationManager extends AbstractPostManager
{
    public function create(Post $post): Post
    {
        if (!($post instanceof Information)) {
            throw new ConflictException('Must be an instance of ' . Information::class);
        }

        $post->setStatus($this->workflowProcessor->getInitialStatus());

        return $this->repository->save($post);
    }
}
