<?php

namespace App\DataFixtures;

use App\Entity\Hebergeur;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class UtilisateurFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        /**
         * @var Faker $faker 
         */

        for ($i = 0; $i < 20; $i++) {
            $faker = Faker\Factory::create();
            $user = new Utilisateur();
            $user->setNom($faker->firstName);
            $user->setPrenom($faker->lastName);
            $user->setEmail($faker->email);
            $user->setTelephone($faker->phoneNumber);
            $user->setRoles(['role' => 'hebergeur']);
            $this->setReference('foreign_key' . $i, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
