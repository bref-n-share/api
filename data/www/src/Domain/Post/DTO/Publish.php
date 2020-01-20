<?php

namespace App\Domain\Post\DTO;

class Publish
{
    /**
     * @var string[]
     */
    private array $channels;

    /**
     * @return string[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @param string[] $channels
     * @return Publish
     */
    public function setChannels(array $channels): Publish
    {
        $this->channels = $channels;

        return $this;
    }
}
