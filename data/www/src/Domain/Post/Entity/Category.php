<?php

namespace App\Domain\Post\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Post\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({"extra-light", "essential", "full"})
     */
    private UuidInterface $id;

    /**
     * @Assert\NotBlank(message="Le nom ne doit pas être vide")
     * @Assert\NotNull(message="Le nom ne doit pas être vide")
     * @Assert\Length(
     *     min="2",
     *     minMessage="Le titre d'une catégorie doit comporter 2 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"extra-light", "essential", "full"})
     */
    private string $title;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
