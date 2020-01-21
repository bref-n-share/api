<?php

namespace App\Domain\Notification\DTO;

class PostNotificationCreate
{
    private ?\DateTimeImmutable $expirationDate = null;

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}
