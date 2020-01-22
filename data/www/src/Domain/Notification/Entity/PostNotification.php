<?php

namespace App\Domain\Notification\Entity;

use App\Domain\Post\Entity\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Notification\Repository\PostNotificationRepository")
 */
class PostNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Post\Entity\Post")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"essential", "full"})
     */
    private Post $post;

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
