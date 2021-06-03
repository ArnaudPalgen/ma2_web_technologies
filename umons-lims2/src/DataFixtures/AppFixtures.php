<?php

namespace App\DataFixtures;

use App\Entity\Hazard;
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
    private $faker;

    private $locations;

    private $hazards;


    private $users;

    private $roles;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        $this->addRoles($manager);
        $this->addLocations($manager);
        $this->addHazards($manager);

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

    private function addHazards(ObjectManager $manager)
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
        foreach ($list as $hazard) {
            $cs = new Hazard();
            $cs->setLabel($hazard);
            $cs->setId("GHS".$this->faker->randomNumber(2));
            $manager->persist($cs);
            $this->hazards[] = $cs;
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
                ->setConcentration(rand(10, 1000))
                ->setLocation($this->faker->randomElement($this->locations))
                ->setSize($this->faker->randomElement(['1 kg', '12 L']));

            $cs = $this->faker->randomElements($this->hazards, rand(0, count($this->hazards)));
            foreach ($cs as $hazard) {
                $product->addHazard($hazard);
            }
            $manager->persist($product);

            if ($this->faker->boolean()) {
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