<?php

namespace App\DataFixtures;

use App\Entity\Worker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{


    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 80; $i++) {
            $rank = '';
            if (rand(0,100) < 20) {
                $rank = "N1";
            } elseif (rand(0,100) < 40) {
                $rank = "N2";
            } elseif (rand(0,100) < 70) {
                $rank = "N3";
            } else  {
                $rank = "N4";
            }

            $worker = new Worker();
            $worker->setFirstName($faker->firstName);
            $worker->setLastName($faker->lastName);
            $worker->setRank($rank);
            $manager->persist($worker);
        }

        $manager->flush();
    }
}
