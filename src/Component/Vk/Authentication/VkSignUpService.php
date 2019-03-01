<?php

namespace App\Component\Vk\Authentication;

use App\Component\Vk\DTO\AccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->vkClient = new VKApiClient();
    }

    public function execute(AccessToken $accessToken): User
    {
        $usersInfo = $this->vkClient->users()->get($accessToken->getAccessToken(), [
            'fields' => ['bdate', 'sex', 'contacts'],
        ]);

        $userInfo = \reset($usersInfo);

        $user = (new User($userInfo['first_name']))
            ->addVkToken($accessToken);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
