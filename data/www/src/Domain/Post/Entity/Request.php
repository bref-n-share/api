<?php

namespace App\Domain\Post\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Post\Repository\RequestRepository")
 */
class Request extends Post
{
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Positive
     * @Assert\Type(type="int")
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Groups({"essential", "full", "creation"})
     */
    private int $requestedQuantity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Groups({"essential", "full"})
     */
    private int $currentQuantity = 0;

    /**
     * @Assert\Valid
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Post\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"essential", "full", "creation"})
     */
    private Category $category;

    public function getRequestedQuantity(): ?int
    {
        return $this->requestedQuantity;
    }

    public function setRequestedQuantity(?int $requestedQuantity): self
    {
        $this->requestedQuantity = $requestedQuantity;

        return $this;
    }

    public function getCurrentQuantity(): ?int
    {
        return $this->currentQuantity;
    }

    public function setCurrentQuantity(?int $currentQuantity): self
    {
        $this->currentQuantity = $currentQuantity;

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
