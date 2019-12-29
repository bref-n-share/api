<?php

namespace App\Infrastructure\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\User\MemberRepository")
 */
class Member extends User
{

}
