<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AuthenticationTest extends ApiTestCase
{

    public function testLogin(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'adri@gmail.com',
                'password' => '1234',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/api/questions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $json['token'],
            ],
        ]);
        $this->assertResponseIsSuccessful();
    }
}