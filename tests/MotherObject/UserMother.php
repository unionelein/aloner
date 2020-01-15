<?php declare(strict_types=1);

namespace App\Tests\MotherObject;

use App\Component\Util\Date;
use App\Component\Vk\DTO\AccessToken;
use App\Entity\City;
use App\Entity\User;
use App\Entity\VO\SearchCriteria;
use App\Entity\VO\Sex;
use App\Entity\VO\VkExtension;
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
        $accessToken = new AccessToken(1123, 'token', new DateTime('+1 year'));

        $user = new User('Vitaliy Cal\'');
        $user->setCity($city)
            ->setBirthday(new DateTime('19.10.1999'))
            ->setSex(new Sex(Sex::MALE))
            ->setVk(new VkExtension($accessToken));

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