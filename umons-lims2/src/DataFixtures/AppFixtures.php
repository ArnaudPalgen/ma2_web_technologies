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
        $this->roles[] = (new Role())->setName("superuser");
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

        $explosive = (new Hazard())
            ->setId('GHS01')
            ->setLabel('Explosive');
        $flammable = (new Hazard())
            ->setId('GHS02')
            ->setLabel('Flammable');
        $oxidizing = (new Hazard())
            ->setId('GHS03')
            ->setLabel('Oxidizing');
        $compressed_gas = (new Hazard())
            ->setId('GHS04')
            ->setLabel('Compressed Gas');
        $corrosive = (new Hazard())
            ->setId('GHS05')
            ->setLabel('Corrosive');
        $toxic = (new Hazard())
            ->setId('GHS06')
            ->setLabel('Toxic');
        $harmful = (new Hazard())
            ->setId('GHS07')
            ->setLabel('Harmful');
        $health_hazard = (new Hazard())
            ->setId('GHS08')
            ->setLabel('Health Hazard');
        $environmental_hazard = (new Hazard())
            ->setId('GHS09')
            ->setLabel('Environmental Hazard');


        $explosive
            ->addIncompatibility($flammable)
            ->addIncompatibility($oxidizing)
            ->addIncompatibility($compressed_gas)
            ->addIncompatibility($corrosive)
            ->addIncompatibility($toxic)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);

        $flammable
            ->addIncompatibility($oxidizing)
            ->addIncompatibility($compressed_gas)
            ->addIncompatibility($corrosive)
            ->addIncompatibility($toxic)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);

        $oxidizing
            ->addIncompatibility($corrosive)
            ->addIncompatibility($toxic)
            ->addIncompatibility($harmful)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);

        $compressed_gas
            ->addIncompatibility($toxic)
            ->addIncompatibility($harmful)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);




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