<?php

namespace App\Domain\Structure\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Structure\Repository\OrganisationRepository")
 */
class Organisation extends Structure
{
    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Structure\Entity\Site", mappedBy="organisation", orphanRemoval=true)
     *
     * @Groups({"full"})
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
