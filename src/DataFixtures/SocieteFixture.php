<?php

namespace App\DataFixtures;

use App\Entity\Societe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SocieteFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0;$i<20;$i++)
        {
            $soc = new Societe();
            $soc->setNameSoc($faker->company);
            $manager->persist($soc);
        }


        $manager->flush();
    }
}
