<?php

namespace App\Infrastructure\Entity\User;

use App\Infrastructure\Entity\Post\Comment;
use App\Infrastructure\Entity\Structure\Structure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Infrastructure\Entity\Post\Comment", mappedBy="member", orphanRemoval=true)
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

    public function setStructure(?Structure $structure): self
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

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMember($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
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
