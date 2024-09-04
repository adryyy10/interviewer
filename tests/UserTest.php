<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
    public function testCreateGetCollection(): void
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'name' => 'Adria',
                'email' => 'adria@adria.com',
                'password' => hash('md5', '1234'),
                'admin' => true,
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        static::createClient()->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'name' => 'Adria',
                    'email' => 'adria@adria.com',
                    'admin' => true,
                ]
            ]
        ]);
    }
}
