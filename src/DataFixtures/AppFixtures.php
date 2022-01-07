<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
//         create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $product = new User();
            $product->setName('Robert');
            $manager->persist($product);
        }

        $manager->flush();
    }
}