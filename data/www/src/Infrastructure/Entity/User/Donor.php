<?php

namespace App\Infrastructure\Entity\User;

use App\Infrastructure\Entity\Structure\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\User\DonorRepository")
 */
class Donor extends User
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Infrastructure\Entity\Structure\Site", inversedBy="donors")
     */
    private Collection $sites;

    public function __construct()
    {
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
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site)) {
            $this->sites->removeElement($site);
        }

        return $this;
    }
}
