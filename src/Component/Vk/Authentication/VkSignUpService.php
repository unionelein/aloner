<?php

namespace App\Component\Vk\Authentication;

use App\Component\Vk\DTO\AccessToken;
use App\Component\Model\VO\Sex;
use App\Entity\User;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use VK\Actions\Enums\AuthSignupSex;
use VK\Client\VKApiClient;

class VkSignUpService
{
    /**
     * @var VKApiClient
     */
    private $vkClient;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var CityRepository
     */
    private $cityRepo;

    public function __construct(EntityManagerInterface $em, CityRepository $cityRepo)
    {
        $this->em       = $em;
        $this->cityRepo = $cityRepo;

        $this->vkClient = new VKApiClient();
    }

    public function execute(AccessToken $accessToken): User
    {
        $usersInfo = $this->vkClient->users()->get($accessToken->getAccessToken(), [
            'fields' => ['bdate', 'sex', 'city'],
        ]);

        $userInfo = \reset($usersInfo);

        $user = (new User($userInfo['first_name']))
            ->addVkToken($accessToken);

        if (isset($userInfo['city']['title']) && $city = $this->cityRepo->findOneBy(['name' => $userInfo['city']['title']])) {
            $user->setCity($city);
        }

        if (isset($userInfo['sex']) && \in_array($userInfo['sex'], [AuthSignupSex::MALE, AuthSignupSex::FEMALE])) {
            $user->setSex(new Sex($userInfo['sex'] === AuthSignupSex::FEMALE ? Sex::FEMALE : Sex::MALE));
        }

        if (isset($userInfo['bdate'])) {
            $user->setBirthday(new \DateTime($userInfo['bdate']));
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
