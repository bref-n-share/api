<?php

namespace App\Domain\Post\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Post\Repository\InformationRepository")
 */
class Information extends Post
{

}
