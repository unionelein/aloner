<?php declare(strict_types=1);

namespace App\Component\Vk\Authentication;

use App\Component\User\UserManager;
use App\Component\Vk\DTO\AccessToken;
use App\Component\Vk\VkUserProvider;
use App\Entity\User;
use App\Entity\VO\VkExtension;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use VK\Actions\Enums\AuthSignupSex;
use VK\Client\VKApiClient;

class VkSignUpService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CityRepository */
    private $cityRepo;

    /** @var UserManager */
    private $userManager;

    /** @var VkUserProvider */
    private $vkUserProvider;

    /**
     * @param EntityManagerInterface $em
     * @param CityRepository         $cityRepo
     * @param UserManager            $userManager
     */
    public function __construct(
        EntityManagerInterface $em,
        CityRepository $cityRepo,
        UserManager $userManager,
        VkUserProvider $vkUserProvider
    ) {
        $this->em          = $em;
        $this->cityRepo    = $cityRepo;
        $this->userManager = $userManager;
        $this->vkUserProvider = $vkUserProvider;
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return User
     *
     * @throws \Exception
     */
    public function execute(AccessToken $accessToken): User
    {
        $vkUserData = $this->vkUserProvider->getByToken($accessToken->getAccessToken());

        $user = $this->userManager->create($vkUserData->getFirstName());
        $user->setVk(new VkExtension($accessToken));

        if ($city = $this->cityRepo->findOneBy(['name' => $vkUserData->getCityName()])) {
            $user->setCity($city);
        }

        if ($sex = $vkUserData->getSex()) {
            $user->setSex($sex);
        }

        if ($birthday = $vkUserData->getBirthday()) {
            $user->setBirthday($birthday);
        }

        if ($photo50 = $vkUserData->getPhoto50()) {
            $user->setAvatarPath($photo50);
        }

        $this->em->flush();

        return $user;
    }
}
