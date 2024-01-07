<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User($this->passwordHasher);
        $user->setUsername("Meaziz")->setPassword("aziz2023");
        $manager->persist($user);
        $faker = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $user = new User($this->passwordHasher);
            $user->setUsername($faker->username())->setPassword($faker->password());
            $manager->persist($user);
        }
        $manager->flush();
    }
}