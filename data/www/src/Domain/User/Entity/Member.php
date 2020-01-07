<?php

namespace App\Domain\User\Entity;

use App\Domain\Post\Entity\Comment;
use App\Domain\Structure\Entity\Structure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\User\Repository\MemberRepository")
 */
class Member extends User
{
    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Domain\Structure\Entity\Structure",
     *      inversedBy="members",
     *      cascade={"persist"}
     * )
     */
    private ?Structure $structure = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Post\Entity\Comment", mappedBy="member", orphanRemoval=true)
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
