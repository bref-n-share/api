<?php

namespace App\Infrastructure\Entity\User;

use App\Infrastructure\Entity\Structure\Structure;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\User\MemberRepository")
 */
class Member extends User
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Infrastructure\Entity\Structure\Structure", inversedBy="members")
     */
    private Structure $structure;

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): self
    {
        $this->structure = $structure;

        return $this;
    }
}
