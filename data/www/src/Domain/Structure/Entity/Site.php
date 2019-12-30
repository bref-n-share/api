<?php

namespace App\Domain\Structure\Entity;

use App\Domain\Flash\Entity\FlashNews;
use App\Domain\User\Entity\Donor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Structure\Repository\SiteRepository")
 */
class Site extends Structure
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Structure\Entity\Organisation", inversedBy="sites")
     * @ORM\JoinColumn(nullable=false)
     */
    private Organisation $organisation;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\User\Entity\Donor", mappedBy="sites")
     */
    private Collection $donors;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Flash\Entity\FlashNews", mappedBy="site", orphanRemoval=true)
     */
    private Collection $flashNews;

    public function __construct()
    {
        parent::__construct();
        $this->donors = new ArrayCollection();
        $this->flashNews = new ArrayCollection();
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

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
     * @return Collection|FlashNews[]
     */
    public function getFlashNews(): Collection
    {
        return $this->flashNews;
    }

    public function addFlashNews(FlashNews $flashNews): self
    {
        if (!$this->flashNews->contains($flashNews)) {
            $this->flashNews[] = $flashNews;
            $flashNews->setSite($this);
        }

        return $this;
    }

    public function removeFlashNews(FlashNews $flashNews): self
    {
        if ($this->flashNews->contains($flashNews)) {
            $this->flashNews->removeElement($flashNews);
            // set the owning side to null (unless already changed)
            if ($flashNews->getSite() === $this) {
                $flashNews->setSite(null);
            }
        }

        return $this;
    }
}
