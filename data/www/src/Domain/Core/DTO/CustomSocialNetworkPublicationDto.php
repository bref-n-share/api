<?php

namespace App\Domain\Core\DTO;

use App\Domain\Structure\Entity\Structure;

class CustomSocialNetworkPublicationDto
{
    private string $message;

    private ?Structure $structure;

    private array $channels;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $author): self
    {
        $this->structure = $author;

        return $this;
    }

    public function getChannels()
    {
        return $this->channels;
    }

    public function setChannels(array $channels): self
    {
        $this->channels = $channels;

        return $this;
    }
}
