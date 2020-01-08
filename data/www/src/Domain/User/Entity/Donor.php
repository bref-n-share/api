<?php

namespace App\Domain\User\Entity;

use App\Domain\Structure\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Domain\User\Repository\DonorRepository")
 */
class Donor extends User
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\Structure\Entity\Site", inversedBy="donors")
     *
     * @Groups({"essential", "full"})
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
