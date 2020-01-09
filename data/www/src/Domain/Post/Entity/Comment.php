<?php

namespace App\Domain\Post\Entity;

use App\Domain\User\Entity\Member;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Post\Repository\CommentRepository")
 */
class Comment
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
     * @Assert\NotBlank(message="La description ne doit pas être vide")
     * @Assert\NotNull(message="La description ne doit pas être vide")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Votre commentaire doit comporter 5 caractères minimum"
     * )
     *
     * @ORM\Column(type="text")
     *
     * @Groups({"essential", "full"})
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Entity\Member", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"essential", "full"})
     */
    private Member $member;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Post\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"full"})
     */
    private Post $post;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"full"})
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"full"})
     */
    private DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = DateTime::createFromImmutable($this->createdAt);
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
