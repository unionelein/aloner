<?php

namespace App\Component\Vk\Authentication;

use App\Component\Vk\DTO\AccessToken;
use App\Component\Vk\VkClient;

class VkAuthService
{
    /**
     * @var VkClient
     */
    private $vkClient;

    public function __construct(VkClient $vkClient)
    {
        $this->vkClient = $vkClient;
    }

    public function getAccessToken(string $accessCode, string $redirectUrl): AccessToken
    {
        return new AccessToken(100002, md5('hello_friend'), null);
    }
}