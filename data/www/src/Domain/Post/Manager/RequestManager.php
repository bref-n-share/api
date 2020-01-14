<?php

namespace App\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Post\DTO\PostEdit;
use App\Domain\Post\DTO\RequestEdit;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Entity\Request;

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

    public function getUpdatedEntity(PostEdit $postEdit, Post $entityToSave): Post
    {
        if (!(($postEdit instanceof RequestEdit) && ($entityToSave instanceof Request))) {
            throw new ConflictException('Must be an instance of ' . RequestEdit::class);
        }

        /** @var Request $entityToSave */
        $entityToSave = parent::getUpdatedEntity($postEdit, $entityToSave);

        return $entityToSave
            ->setCategory($postEdit->getCategory() ?? $entityToSave->getCategory())
            ->setRequestedQuantity($postEdit->getRequestedQuantity() ?? $entityToSave->getRequestedQuantity())
        ;
    }
}
