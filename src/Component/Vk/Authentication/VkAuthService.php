<?php declare(strict_types=1);

namespace App\Component\Vk\Authentication;

use App\Component\Vk\DTO\AccessToken;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class VkAuthService
{
    /** @var VKOAuth */
    private $vkOAuth;

    /** @var int */
    private $vkAppId;

    /** @var string */
    private $vkAppSecret;

    public function __construct(int $vkAppId, string $vkAppSecret)
    {
        $this->vkOAuth = new VKOAuth();
        $this->vkAppId = $vkAppId;
        $this->vkAppSecret = $vkAppSecret;
    }

    public function getAccessToken(string $accessCode, string $redirectUrl): ?AccessToken
    {
        try {
            $response = $this->vkOAuth->getAccessToken($this->vkAppId, $this->vkAppSecret, $redirectUrl, $accessCode);

            $expiresAt = $response['expires_in'] > 0
                ? new \DateTime("+{$response['expires_in']} sec")
                : null;

            return new AccessToken($response['user_id'], $response['access_token'], $expiresAt);

        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAuthorizeUrl(string $redirectUrl, array $scope = []): string
    {
        return $this->vkOAuth->getAuthorizeUrl(
            VKOAuthResponseType::CODE,
            $this->vkAppId,
            $redirectUrl,
            VKOAuthDisplay::PAGE,
            $scope
        );
    }
}
