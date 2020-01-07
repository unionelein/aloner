<?php declare(strict_types=1);

namespace App\Tests\MotherObject;

use App\Component\Util\Date;
use App\Entity\City;
use App\Entity\User;
use App\Entity\VO\SearchCriteria;
use App\Entity\VO\Sex;
use \DateTime;

class UserMother
{
    /**
     * @param City $city
     *
     * @return User
     */
    public static function create(City $city): User
    {
        $user = new User('Vitaliy Cal\'');
        $user->setCity($city)
            ->setBirthday(new DateTime('19.10.1999'))
            ->setSex(new Sex(Sex::MALE));

        return $user;
    }

    /**
     * @param City $city
     *
     * @return User
     */
    public static function withTodayCriteria(City $city): User
    {
        $searchCriteria = new SearchCriteria(
            Date::date('now'),
            Date::time('00:00'),
            Date::time('23:59')
        );

        $user = self::create($city);
        $user->setSearchCriteria($searchCriteria);

        return $user;
    }
}