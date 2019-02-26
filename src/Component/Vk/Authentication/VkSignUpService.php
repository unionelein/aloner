<?php

namespace App\Component\Vk\Authentication;

use App\Component\Vk\DTO\AccessToken;
use App\Component\Vk\VkApiProvider;
use App\Entity\User;
use App\Entity\VkUserToken;
use Doctrine\ORM\EntityManagerInterface;

class VkSignUpService
{
    /**
     * @var VkApiProvider
     */
    private $vkProvider;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(VkApiProvider $vkProvider, EntityManagerInterface $em)
    {
        $this->vkProvider = $vkProvider;
        $this->em = $em;
    }

    public function execute(AccessToken $accessToken): User
    {
        $accountInfo = $this->vkProvider->getAccountInfo($accessToken);

        $user = (new User($accountInfo['name']))
            ->addVkToken($accessToken)
            ->setPhone($accountInfo['phone']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}