<?php

namespace App\DataFixtures;

use App\Entity\Hebergeur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class HebergeurFixtures extends Fixture implements DependentFixtureInterface
{




    /**
     * documentation : https://github.com/fzaninotto/Faker#fakerprovideren_usphonenumber
     */
    public function load(ObjectManager $manager): void
    {
        /**
         * @var Faker $faker 
         */

        for ($i = 0; $i < 20; $i++) {
            $faker = Faker\Factory::create();
            $hebergeur = new Hebergeur();
            $hebergeur->setAdresse($faker->address);
            $hebergeur->setVille($faker->city);
            $hebergeur->setCodePostal($faker->postcode);

            $hebergeur->setIdUtilisateurFk($this->getReference('foreign_key' . $i));


            $manager->persist($hebergeur);
        }

        $manager->flush();
    }



    public function getDependencies()
    {
        return [
            UtilisateurFixtures::class
        ];
    }
}
