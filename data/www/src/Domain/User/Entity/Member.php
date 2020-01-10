<?php

namespace App\Domain\User\Entity;

use App\Domain\Post\Entity\Comment;
use App\Domain\Structure\Entity\Structure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Member extends User
{
    /**
     * @Assert\Valid
     * @Assert\NotNull(message="La structure doit être définie")
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Domain\Structure\Entity\Structure",
     *      inversedBy="members",
     *      cascade={"persist"}
     * )
     *
     * @Groups({"essential", "full", "creation"})
     */
    private ?Structure $structure = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Post\Entity\Comment", mappedBy="member", orphanRemoval=true)
     *
     * @Groups({"full"})
     */
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): Member
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): Member
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMember($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): Member
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getMember() === $this) {
                $comment->setMember(null);
            }
        }

        return $this;
    }
}
