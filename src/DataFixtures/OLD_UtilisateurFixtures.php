<?php

namespace App\DataFixtures;

use App\Entity\Hebergeur;
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
            $hebergeur = new Hebergeur();
            $hebergeur->setNom($faker->name);
            $hebergeur->setPrenom($faker->lastName);
            $hebergeur->setEmail($faker->email);
            $hebergeur->setTelephone($faker->phoneNumber);
            $hebergeur->setRoles(['role' => 'hebergeur']);
            //$this->setReference(self::FOREIGN_KEY_HEBERGEUR, $hebergeur);

            $manager->persist($hebergeur);
        }

        $manager->flush();
    }
}
