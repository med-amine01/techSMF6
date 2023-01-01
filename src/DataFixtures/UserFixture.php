<?php

namespace App\DataFixtures;

use App\Entity\SystemUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $admin1 = new SystemUser();
        $admin1->setEmail('admin@gmail.com');
        $admin1->setPassword($this->hasher->hashPassword($admin1,'admin'));
        $admin1->setRoles(['ROLE_ADMIN']);

        $admin2 = new SystemUser();
        $admin2->setEmail('admin2@gmail.com');
        $admin2->setPassword($this->hasher->hashPassword($admin2,'admin'));
        $admin2->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin1);
        $manager->persist($admin2);

        for($i=0;$i<=5;$i++)
        {
            $user = new SystemUser();
            $user->setEmail("user$i@gmail.com");
            $user->setPassword($this->hasher->hashPassword($user,'user'));
            //by default user will get ['ROLE_USER']
            $manager->persist($user);
        }
        $manager->flush();
    }
}
