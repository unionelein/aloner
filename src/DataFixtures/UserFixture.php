<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;

class UserFixture extends BaseFixture
{
    protected function loadData()
    {
        $this->createMany('user', 1, function (int $index) {
            $user = new User('WEB');

            $reflection = new \ReflectionObject($user);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($user, User::WEB_ID);

            return $user;
        });
    }
}