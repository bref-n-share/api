<?php

namespace App\Domain\Core\Publication;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;

class CustomPublicationProcessorChain
{
    /**
     * @var CustomPublicationProcessorInterface[]
     */
    private iterable $processors;

    /**
     * NotificatorProcessorChain constructor.
     *
     * @param iterable $processors
     */
    public function __construct(iterable $processors)
    {
        $this->processors = $processors;
    }

    public function handle(CustomSocialNetworkPublicationDto $publicationDto, string $channel): bool
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($channel)) {
                return $processor->handle($publicationDto);
            }
        }

        throw new \InvalidArgumentException('No processor can handle this channel (' . $channel . ')');
    }
}
