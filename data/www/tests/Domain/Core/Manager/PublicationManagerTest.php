<?php

namespace App\Tests\Domain\Core\Manager;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;
use App\Domain\Core\Manager\PublicationManager;
use App\Domain\Core\Publication\CustomPublicationProcessorChain;
use PHPUnit\Framework\TestCase;

class PublicationManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $publicationProcessorChain;

    /** @var PublicationManager */
    private $manager;

    protected function setUp()
    {
        $this->publicationProcessorChain = $this->createMock(CustomPublicationProcessorChain::class);

        $this->manager = new PublicationManager($this->publicationProcessorChain);
    }

    public function testPublish(): void
    {
        $socialNetworkNotificationDto = $this->createMock(CustomSocialNetworkPublicationDto::class);

        $this->publicationProcessorChain
            ->expects($this->exactly(2))
            ->method('handle')
            ->withConsecutive(
                [$socialNetworkNotificationDto, 'facebook'],
                [$socialNetworkNotificationDto, 'twitter']
            )
        ;

        $this->manager->publish($socialNetworkNotificationDto, ['facebook', 'twitter']);
    }
}
