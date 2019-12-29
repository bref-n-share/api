<?php

namespace App\Infrastructure\Entity\Structure;

use App\Infrastructure\Entity\User\Donor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\Structure\SiteRepository")
 */
class Site extends Structure
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Infrastructure\Entity\Structure\Organisation", inversedBy="sites")
     * @ORM\JoinColumn(nullable=false)
     */
    private Organisation $organisation;

    /**
     * @ORM\ManyToMany(targetEntity="App\Infrastructure\Entity\User\Donor", mappedBy="sites")
     */
    private Collection $donors;

    public function __construct()
    {
        parent::__construct();
        $this->donors = new ArrayCollection();
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
}
