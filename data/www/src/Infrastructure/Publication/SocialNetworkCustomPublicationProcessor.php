<?php

namespace App\Infrastructure\Publication;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;
use App\Domain\Core\Publication\CustomPublicationProcessorInterface;
use MartinGeorgiev\SocialPost\Message;
use MartinGeorgiev\SocialPost\Publisher;

class SocialNetworkCustomPublicationProcessor implements CustomPublicationProcessorInterface
{
    private string $type;

    private Publisher $publisher;

    public function __construct(string $type, Publisher $publisher)
    {
        $this->type = $type;
        $this->publisher = $publisher;
    }

    public function supports(string $channel): bool
    {
        return strtolower($channel) === strtolower($this->type);
    }

    public function handle(CustomSocialNetworkPublicationDto $publicationDto): bool
    {
        $message = new Message($publicationDto->getMessage() . " - " . $publicationDto->getStructure()->getName());
        $message->setNetworksToPublishOn([$this->type]);

        return $this->publisher->publish($message);
    }
}
