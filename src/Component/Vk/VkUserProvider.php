<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Vk;

use App\Component\Vk\DTO\VkUserData;
use App\Entity\VO\Sex;
use VK\Actions\Enums\AccountSex;
use VK\Client\VKApiClient;
use Webmozart\Assert\Assert;

class VkUserProvider
{
    private const SEX = [
        AccountSex::MALE   => Sex::MALE,
        AccountSex::FEMALE => Sex::FEMALE,
    ];

    public const ADDITIONAL_FIELDS = [
        'bdate',
        'sex',
        'city',
        'photo_50',
    ];

    /**
     * @param string $accessToken
     * @param array  $fields
     *
     * @return VkUserData
     *
     * @throws \Exception
     */
    public function getByToken(string $accessToken, array $fields = self::ADDITIONAL_FIELDS): VkUserData
    {
        $users = (new VKApiClient())->users()->get($accessToken, ['fields' => $fields]);
        Assert::count($users, 1, 'More than 1 user found for access token');

        $userData = \reset($users);
        Assert::notNull($userData, 'VK User not found');

        $userId  = (int) $userData['id'];
        $first   = $userData['first_name'];
        $last    = $userData['last_name'];
        $city    = $userData['city']['title'] ?? null;
        $photo50 = $userData['photo_50'];

        $sexVal = $userData['sex'] ?? null;
        $sex    = isset(self::SEX[$sexVal]) ? new Sex(self::SEX[$sexVal]) : null;

        $bdate = \preg_match('#^\d{1,2}\.\d{1,2}.\d{1,4}$#', $userData['bdate'] ?? '')
            ? new \DateTime($userData['bdate'])
            : null;

        return (new VkUserData($userId, $first, $last))
            ->setCityName($city)
            ->setPhoto50($photo50)
            ->setSex($sex)
            ->setBirthday($bdate);
    }
}
