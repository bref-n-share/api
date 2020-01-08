<?php

namespace App\Domain\Structure\Entity;

use App\Domain\User\Entity\Member;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"organization" = "Organization", "site" = "Site"})
 * @DiscriminatorMap(typeProperty="type", mapping={
 *    "organization"="App\Domain\Structure\Entity\Organization",
 *    "site"="App\Domain\Structure\Entity\Site"
 * })
 */
abstract class Structure
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
     *     minMessage="Votre nom doit comporter 2 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"extra-light", "essential", "full", "creation"})
     */
    private string $name;

    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(
     *     min="2",
     *     minMessage="Votre adresse doit comporter 2 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full", "creation"})
     */
    private string $address;

    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(
     *     min="5",
     *     max="5",
     *     minMessage="Veuillez entrer un code postal valide (5 caractères)"
     * )
     *
     * @ORM\Column(type="string", length=5)
     *
     * @Groups({"essential", "full", "creation"})
     */
    private string $postalCode;

    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(
     *     min="2",
     *     minMessage="Votre ville doit comporter 2 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full", "creation"})
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full"})
     */
    private string $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\User\Entity\Member", mappedBy="structure")
     *
     * @Groups({"full"})
     */
    private Collection $members;

    /**
     * @Assert\Regex(pattern="/^((\+)33|0)[1-9](\d{2}){4}$/")
     *
     * @ORM\Column(type="string", length=12, nullable=true)
     *
     * @Groups({"essential", "full", "creation"})
     */
    private ?string $phone = null;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    /**
     * @return Collection|Member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setStructure($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            // set the owning side to null (unless already changed)
            if ($member->getStructure() === $this) {
                $member->setStructure(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
