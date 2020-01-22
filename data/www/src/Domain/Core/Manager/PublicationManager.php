<?php

namespace App\Domain\Core\Manager;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;
use App\Domain\Core\Publication\CustomPublicationProcessorChain;

class PublicationManager
{
    private CustomPublicationProcessorChain $processorChain;

    public function __construct(CustomPublicationProcessorChain $processorChain)
    {
        $this->processorChain = $processorChain;
    }

    public function publish(
        CustomSocialNetworkPublicationDto $socialNetworkPublicationDto,
        array $channels
    ): CustomSocialNetworkPublicationDto {
        foreach ($channels as $channel) {
            $this->processorChain->handle($socialNetworkPublicationDto, $channel);
        }

        return $socialNetworkPublicationDto;
    }
}
