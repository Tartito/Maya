<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
 
use Faker\Factory;
use Faker\Generator;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $unPasswordHasher)
    {
        $this->passwordHasher = $unPasswordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
         // créer 10 utilisateurs
         for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setNom($this->faker->lastName);
            $user->setPrenom($this->faker->firstName);
            $user->setTelephone(substr($this->faker->e164PhoneNumber, 2, 10 ));
            $user->setEmail(sprintf('userdemo%d@exemple.com', $i));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'userdemo'));

            
            if ($i == 0) {
                $user->setRoles(array("ROLE_USER", "ROLE_ADMIN"));
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
