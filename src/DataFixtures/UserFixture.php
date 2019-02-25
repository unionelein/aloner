<?php

namespace App\DataFixtures;

use App\Entity\User;

class UserFixture extends BaseFixture
{
    public function loadData()
    {
        $this->createMany('user', 10, function (int $index) {
            $user = new User();
            $user->setLogin('id' . $this->faker->numberBetween(1000000, 9999999));
            $user->setName($this->faker->name);

            return $user;
        });
    }
}
