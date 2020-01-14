<?php

namespace App\Domain\Post\DTO;

use App\Domain\Post\Entity\Category;

class RequestEdit extends PostEdit
{
    private ?int $requestedQuantity = null;

    private ?Category $category = null;

    public function getRequestedQuantity(): ?int
    {
        return $this->requestedQuantity;
    }


    public function setRequestedQuantity(?int $requestedQuantity): self
    {
        $this->requestedQuantity = $requestedQuantity;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
