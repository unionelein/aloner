<?php

namespace App\Component\Vk;

use App\Component\Vk\DTO\AccessToken;

class VkApiProvider
{
    /**
     * @var VkClient
     */
    private $vkClient;

    public function __construct(VkClient $vkClient)
    {
        $this->vkClient = $vkClient;
    }

    public function getAccountInfo(AccessToken $accessToken)
    {
        return [
            'name' => 'Лёша',
            'phone' => '+2342382553',
        ];
    }
}