<?php

namespace App\DataFixtures;

use App\Entity\ChemicalSafety;
use App\Entity\Location;
use App\Entity\Product;
use App\Entity\Role;
use App\Entity\Usage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private  $faker;

    private $locations;

    private $chemical_safeties;



    private $users;

    private $roles;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        $this->addRoles($manager);
        $this->addLocations($manager);
        $this->addChemicalSafeties($manager);

        $this->addUsers($manager);
        $this->addProducts($manager);



        $manager->flush();
    }

    private function addRoles(ObjectManager $manager)
    {
        $this->roles[] = (new Role())->setName("sudo");
        $this->roles[] = (new Role())->setName("admin");
        $this->roles[] = (new Role())->setName("user");

        foreach ($this->roles as $role) {
            $manager->persist($role);
        }

    }

    private function addLocations(ObjectManager $manager)
    {
        $this->locations = [];

        foreach (range('A', 'Q') as $shelf) {
            for ($level = 0; $level < 4; $level++) {
                $location = new Location();
                $location
                    ->setLevel($level)
                    ->setShelf($shelf);
                $manager->persist($location);

                $this->locations[] = $location;
            }
        }

    }

    private function addChemicalSafeties(ObjectManager $manager)
    {
        $list = [
            "Flammable",
            "Compressed Gases",
            "Explosives",
            "Organic Peroxides",
            "Reactives",
            "Oxidizers",
            "Pyrophorics",
            "Health Hazards",
            "Carcinogens",
            "Reproductive Toxins",
            "Irritants",
            "Corrosives",
            "Sensitizers",
            "Hepatotoxins",
            "Nephrotoxins",
        ];
        foreach ($list as $safety) {
            $cs = new ChemicalSafety();
            $cs->setName($safety);
            $manager->persist($cs);
            $this->chemical_safeties[] = $cs;
        }
    }

    private function addUsers(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {

            $user = new User();
            $user
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->firstName())
                ->setRegistrationNumber(strval($this->faker->randomNumber(6)))
                ->setRole($this->faker->randomElement($this->roles));


            $manager->persist($user);

            $this->users[] = $user;
        }



    }



    private function addProducts(ObjectManager $manager)
    {


        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product
                ->setName($this->faker->word())
                ->setNcas($this->faker->randomNumber(7))
                ->setConcentration(rand(10,1000))
                ->setLocation($this->faker->randomElement($this->locations))
                ->setVolume('1 L')
                ->setMass('15 kg');

            $cs = $this->faker->randomElements($this->chemical_safeties, rand(0, count($this->chemical_safeties)));
            foreach ($cs as $safety) {
                $product->addChemicalSafety($safety);
            }
            $manager->persist($product);

            if($this->faker->boolean()) {
                $usage = new Usage();
                $d = $this->faker->dateTimeThisYear();
                $usage
                    ->setAction(Usage::ACTION_TAKE)
                    ->setProduct($product)
                    ->setUser($this->faker->randomElement($this->users))
                    ->setDate($d);
                $manager->persist($usage);

                $usage = new Usage();
                $d = $this->faker->dateTimeThisYear($d);
                $usage
                    ->setAction(Usage::ACTION_RETURN)
                    ->setProduct($product)
                    ->setUser($this->faker->randomElement($this->users))
                    ->setDate($d);
                $manager->persist($usage);

                $usage = new Usage();
                $d = $this->faker->dateTimeThisYear($d);
                $usage
                    ->setAction(Usage::ACTION_TAKE)
                    ->setProduct($product)
                    ->setUser($this->faker->randomElement($this->users))
                    ->setDate($this->faker->dateTimeThisYear());
                $manager->persist($usage);
            }
        }

    }



}