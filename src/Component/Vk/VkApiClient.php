<?php

namespace App\Component\Vk;

use GuzzleHttp\ClientInterface;

class VkApiClient
{
    private const API_URL  = 'https://api.vk.com';

    private const AUTH_URL = 'https://oauth.vk.com';

    public const VERSION = '5.92';

    /** @var ClientInterface */
    private $guzzleClient;

    public function __construct(ClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function api(string $uri, array $params = [])
    {
        return $this->request(self::API_URL, $uri, $params);
    }

    public function auth(string $uri, array $params = [])
    {
        return $this->request(self::AUTH_URL, $uri, $params);
    }

    private function request(string $url, string $uri, array $params)
    {
        $params = \array_merge($params, ['v' => self::VERSION]);

        return $this->guzzleClient->request('GET', $url . $uri, [
            'query' => $params,
        ]);
    }
}