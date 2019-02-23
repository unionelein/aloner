<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    /** @var Generator */
    protected $faker;

    abstract protected function loadData();

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();

        $this->loadData();

        $this->manager->flush();
    }

    public function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 1; $i <= $count; $i++) {
            $entity = new $className;
            $factory($entity, $i);

            $this->manager->persist($entity);
            $this->addReference(\get_class($entity) . '_' . $i, $entity);
        }
    }
}