<?php

namespace App\Domain\Notification\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Notification\Repository\SimpleNotificationRepository")
 */
class SimpleNotification extends Notification
{
}
