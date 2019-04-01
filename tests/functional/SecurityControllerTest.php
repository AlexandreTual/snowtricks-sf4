<?php

namespace App\tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/login'],
            ['/registration'],
            ['/mail/confirm'],
        ];
    }

    public function testPageLogoutIsSuccessful()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
