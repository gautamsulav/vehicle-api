<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $vehicle = new Vehicle();
            $vehicle->setDateAdded($faker->dateTime);
            $vehicle->setType($faker->randomElement(['used', 'new']));
            $vehicle->setMsrp($faker->randomFloat());
            $vehicle->setYear($faker->year());
            $vehicle->setMake($faker->randomElement(['Toyota', 'Honda', 'Ford', 'GM', 'Nissan', 'Tesla']));
            $vehicle->setModel($faker->text(20));
            $vehicle->setMiles($faker->randomDigitNotNull);
            $vehicle->setVin($faker->text(10));
            $vehicle->setDeleted(false);
            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
