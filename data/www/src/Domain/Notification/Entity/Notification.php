<?php

namespace App\Domain\Notification\Entity;

use App\Domain\Structure\Entity\Site;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Notification\Repository\NotificationRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"postNotification" = "PostNotification", "simpleNotification" = "SimpleNotification"})
 * @DiscriminatorMap(typeProperty="type", mapping={
 *    "postNotification"="App\Domain\Notification\Entity\PostNotification",
 *    "simpleNotification"="App\Domain\Notification\Entity\SimpleNotification"
 * })
 */
abstract class Notification
{
    private const VALID_STATUS = 'VALID';

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
     * @Assert\NotNull(message="Le titre ne doit pas être vide")
     * @Assert\NotBlank(message="Le titre ne doit pas être vide")
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
     * @Assert\NotBlank(message="La description ne doit pas être vide")
     * @Assert\NotNull(message="La description ne doit pas être vide")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Votre description doit comporter 5 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full"})
     */
    private string $description;

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
    private DateTimeInterface $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Structure\Entity\Site", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"essential", "full"})
     */
    private Site $site;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full"})
     */
    private string $status;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->status === self::VALID_STATUS;
    }
}
