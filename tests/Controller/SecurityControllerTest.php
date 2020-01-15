<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 */
class SecurityControllerTest extends WebTestCase
{
    public function testVkPageReturnsValidPage(): void
    {
        $client  = self::createClient();
        $crawler = $client->request('GET', '/vk_auth');

        self::assertResponseIsSuccessful();

        $authBtn = $crawler->filter('.vk-auth-btn')->getNode(0);
        $this->assertNotNull($authBtn);
    }
}