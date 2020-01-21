<?php

namespace App\Domain\Structure\Entity;

use App\Domain\Notification\Entity\Notification;
use App\Domain\Post\Entity\Post;
use App\Domain\User\Entity\Donor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Structure\Repository\SiteRepository")
 */
class Site extends Structure
{
    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="sites")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"extra-light", "essential", "full", "creation"})
     */
    private Organization $organization;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\User\Entity\Donor", mappedBy="sites")
     *
     * @Groups({"full"})
     */
    private Collection $donors;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Notification\Entity\Notification", mappedBy="site", orphanRemoval=true)
     *
     * @Groups({"full"})
     */
    private Collection $notifications;

    /**
     * @Assert\NotBlank(message="La longitude ne doit pas être vide")
     * @Assert\NotNull(message="La longitude ne doit pas être vide")
     *
     * @ORM\Column(type="decimal", precision=11, scale=8)
     *
     * @Groups({"essential", "full", "creation", "updatable"})
     */
    private string $longitude;

    /**
     * @Assert\NotBlank(message="La latitude ne doit pas être vide")
     * @Assert\NotNull(message="La latitude ne doit pas être vide")
     *
     * @ORM\Column(type="decimal", precision=10, scale=8)
     *
     * @Groups({"essential", "full", "creation", "updatable"})
     */
    private string $latitude;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Post\Entity\Post", mappedBy="site", orphanRemoval=true)
     *
     * @Groups({"full"})
     */
    private Collection $posts;

    public function __construct()
    {
        parent::__construct();
        $this->donors = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Collection|Donor[]
     */
    public function getDonors(): Collection
    {
        return $this->donors;
    }

    public function addDonor(Donor $donor): self
    {
        if (!$this->donors->contains($donor)) {
            $this->donors[] = $donor;
            $donor->addSite($this);
        }

        return $this;
    }

    public function removeDonor(Donor $donor): self
    {
        if ($this->donors->contains($donor)) {
            $this->donors->removeElement($donor);
            $donor->removeSite($this);
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setSite($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getSite() === $this) {
                $notification->setSite(null);
            }
        }

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setSite($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getSite() === $this) {
                $post->setSite(null);
            }
        }

        return $this;
    }
}
