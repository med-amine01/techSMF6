<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Personne;
use App\Entity\Societe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Fakecar;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //php bin/console doctrine:fixtures:load --append
        //php bin/console doctrine:fixtures:load (this will purge the database)

        $faker = (new Factory())::create();
        $faker->addProvider(new Fakecar($faker));

        for ($i=0;$i<100;$i++)
        {
            $personne = new Personne();
            $personne->setFirstname($faker->firstName);
            $personne->setLastname($faker->lastName);
            $personne->setAge($faker->numberBetween(20,50));
            $manager->persist($personne);
        }



        $manager->flush();
    }
}
