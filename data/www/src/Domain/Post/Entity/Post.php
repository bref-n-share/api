<?php

namespace App\Domain\Post\Entity;

use App\Domain\Structure\Entity\Site;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"request" = "Request", "information" = "Information"})
 * @DiscriminatorMap(typeProperty="type", mapping={
 *    "request"="App\Domain\Post\Entity\Request",
 *    "information"="App\Domain\Post\Entity\Information"
 * })
 */
abstract class Post
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
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(
     *     min="2",
     *     minMessage="Votre titre doit comporter 2 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"extra-light", "essential", "full"})
     */
    private string $title;

    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(
     *     min="5",
     *     minMessage="Votre description doit comporter 5 caractères minimum"
     * )
     *
     * @ORM\Column(type="text")
     *
     * @Groups({"essential", "full"})
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full"})
     */
    private string $status;

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

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     *
     * @Groups({"essential", "full"})
     */
    private array $channels = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Post\Entity\Comment", mappedBy="post", orphanRemoval=true)
     *
     * @Groups({"full"})
     */
    private Collection $comments;

    /**
     * @Assert\Valid
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Structure\Entity\Site", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"essential", "full"})
     */
    private Site $site;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = DateTime::createFromImmutable($this->createdAt);
        $this->comments = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
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

    public function getChannels(): ?array
    {
        return $this->channels;
    }

    public function setChannels(?array $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    public function addChannel(string $channel): self
    {
        $this->channels[] = $channel;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }
}
