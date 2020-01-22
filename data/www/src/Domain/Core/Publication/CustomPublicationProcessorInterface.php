<?php

namespace App\Domain\Core\Publication;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;

interface CustomPublicationProcessorInterface
{
    public function supports(string $channel): bool;

    public function handle(CustomSocialNetworkPublicationDto $publicationDto): bool;
}
