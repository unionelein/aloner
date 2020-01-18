<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 */
class LoginControllerTest extends WebTestCase
{
    public function testThatLoginPageWorksCorrectly(): void
    {
        $client  = self::createClient();
        $crawler = $client->request('GET', '/login');

        self::assertResponseIsSuccessful();

        $authBtn = $crawler->filter('.vk-auth-btn')->getNode(0);
        $this->assertNotNull($authBtn);
    }
}