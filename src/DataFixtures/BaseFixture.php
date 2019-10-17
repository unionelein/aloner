<?php declare(strict_types=1);

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

    abstract protected function loadData(): void;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();

        $this->loadData();

        $this->manager->flush();
    }

    /**
     * @param string   $alias
     * @param int      $count
     * @param callable $factory
     */
    public function createMany(string $alias, int $count, callable $factory): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $entity = $factory($i);

            $this->manager->persist($entity);
            $this->addReference("{$alias}_{$i}", $entity);
        }
    }
}
