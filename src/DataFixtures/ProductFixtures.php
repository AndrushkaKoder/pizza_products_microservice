<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 20; $i++) {
            $manager->persist((new Product())
                ->setName('Pizza_' . $i)
                ->setDescription('Descr ' . $i)
                ->setActive(true)
                ->setPrice($i * rand(1, 100))
            );
        }

        $manager->flush();
    }
}