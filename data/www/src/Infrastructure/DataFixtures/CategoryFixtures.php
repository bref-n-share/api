<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Post\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->persist((new Category())->setTitle("Vêtements"));
        $manager->persist((new Category())->setTitle("Alimentaire"));

        $manager->flush();
    }
}
