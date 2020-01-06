<?php

namespace App\Infrastructure\Entity\Post;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\Post\InformationRepository")
 */
class Information extends Post
{

}
