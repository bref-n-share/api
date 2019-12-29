<?php

namespace App\Infrastructure\Entity\Structure;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\Structure\OrganisationRepository")
 */
class Organisation extends Structure
{
    /**
     * @ORM\OneToMany(targetEntity="App\Infrastructure\Entity\Structure\Site", mappedBy="organisation", orphanRemoval=true)
     */
    private Collection $sites;

    public function __construct()
    {
        parent::__construct();

        $this->sites = new ArrayCollection();
    }

    /**
     * @return Collection|Site[]
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setOrganisation($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site)) {
            $this->sites->removeElement($site);
            // set the owning side to null (unless already changed)
            if ($site->getOrganisation() === $this) {
                $site->setOrganisation(null);
            }
        }

        return $this;
    }
}
