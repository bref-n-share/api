<?php

namespace App\Domain\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\User\Repository\UserRepository")
 * @ORM\Table(name="user_account")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"member" = "Member", "donor" = "Donor"})
 */
abstract class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({"extra-light", "essential", "full"})
     */
    private ?UuidInterface $id;

    /**
     * @Assert\Email(message="L'email n'est pas valide")
     * @Assert\NotNull(message="L'email ne doit pas être vide")
     * @Assert\NotBlank(message="L'email ne doit pas être vide")
     *
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Groups({"full", "creation"})
     */
    private string $email;

    /**
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     *
     * @ORM\Column(type="json")
     *
     * @Groups({"essential", "full"})
     */
    private array $roles = [];

    /**
     * 8 characters
     * 1 lower character
     * 1 upper character
     * 1 numeric character
     * 1 special character
     *
     * @var string The hashed password
     *
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/",
     *     message="Le mot de passe doit être composé de 8 caractères minimum, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial"
     * )
     * @Assert\NotNull(message="Le mot de passe ne doit pas être vide")
     * @Assert\NotBlank(message="Le mot de passe ne doit pas être vide")
     *
     * @ORM\Column(type="string")
     *
     * @Groups({"creation"})
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full"})
     */
    private string $status;

    /**
     * @Assert\NotBlank(message="Le prénom ne doit pas être vide")
     * @Assert\NotNull(message="Le prénom ne doit pas être vide")
     * @Assert\Length(
     *     min="2",
     *     minMessage="Votre prénom doit comporter 2 caractères minimum"
     * )

     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full", "creation", "updatable"})
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="Le nom ne doit pas être vide")
     * @Assert\NotNull(message="Le nom ne doit pas être vide")
     * @Assert\Length(
     *     min="2",
     *     minMessage="Votre nom doit comporter 2 caractères minimum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"essential", "full", "creation", "updatable"})
     */
    private string $lastName;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @Groups({"extra-light", "essential", "full"})
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
