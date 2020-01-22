<?php

namespace App\Tests\Domain\Core\Manager;

use App\Domain\Notification\DTO\SimpleNotificationCreate;
use App\Domain\Notification\Entity\PostNotification;
use App\Domain\Notification\Entity\SimpleNotification;
use App\Domain\Notification\Manager\NotificationFactory;
use App\Domain\Post\Entity\Post;
use App\Domain\Structure\Entity\Site;
use PHPUnit\Framework\TestCase;

class NotificationFactoryTest extends TestCase
{
    /** @var NotificationFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new NotificationFactory();
    }

    public function testCreatePostNotification(): void
    {
        $createdAt = new \DateTimeImmutable();
        $expirationDate = new \DateTimeImmutable();

        $post = $this->createMock(Post::class);
        $site = $this->createMock(Site::class);

        $postNotification = (new PostNotification())
            ->setPost($post)
            ->setTitle('title')
            ->setDescription('description')
            ->setExpirationDate($expirationDate)
            ->setSite($site)
            ->setCreatedAt($createdAt)
        ;

        $post
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn('title')
        ;

        $post
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('description')
        ;

        $post
            ->expects($this->once())
            ->method('getSite')
            ->willReturn($site)
        ;

        $actual = $this->factory->createPostNotification($post, $expirationDate);
        $actual->setCreatedAt($createdAt);

        $this->assertEquals($postNotification, $actual);
    }

    public function testCreateSimpleNotification(): void
    {
        $createdAt = new \DateTimeImmutable();
        $expirationDate = new \DateTimeImmutable();

        $simpleNotificationCreate = $this->createMock(SimpleNotificationCreate::class);
        $site = $this->createMock(Site::class);

        $simpleNotification = (new SimpleNotification())
            ->setTitle('title')
            ->setDescription('description')
            ->setExpirationDate($expirationDate)
            ->setSite($site)
            ->setCreatedAt($createdAt)
        ;

        $simpleNotificationCreate
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn('title')
        ;

        $simpleNotificationCreate
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('description')
        ;

        $simpleNotificationCreate
            ->expects($this->once())
            ->method('getSite')
            ->willReturn($site)
        ;

        $simpleNotificationCreate
            ->expects($this->once())
            ->method('getExpirationDate')
            ->willReturn($expirationDate)
        ;

        $actual = $this->factory->createSimpleNotification($simpleNotificationCreate);
        $actual->setCreatedAt($createdAt);

        $this->assertEquals($simpleNotification, $actual);
    }
}
