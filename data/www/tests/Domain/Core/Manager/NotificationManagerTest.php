<?php

namespace App\Tests\Domain\Core\Manager;

use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;
use App\Domain\Core\Manager\NotificationManager;
use App\Domain\Core\Notification\CustomNotificationProcessorChain;
use PHPUnit\Framework\TestCase;

class NotificationManagerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $notificationProcessorChain;

    /** @var NotificationManager */
    private $manager;

    protected function setUp()
    {
        $this->notificationProcessorChain = $this->createMock(CustomNotificationProcessorChain::class);

        $this->manager = new NotificationManager($this->notificationProcessorChain);
    }

    public function testPublish(): void
    {
        $socialNetworkNotificationDto = $this->createMock(CustomSocialNetworkNotificationDto::class);

        $this->notificationProcessorChain
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
