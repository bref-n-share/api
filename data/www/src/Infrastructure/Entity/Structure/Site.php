<?php

namespace App\Infrastructure\Entity\Structure;

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

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }
}
