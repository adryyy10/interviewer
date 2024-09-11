<?php

namespace App\Tests;

class UserTest extends InterviewerTestCase
{

    public function testCreateGetCollection(): void
    {
        $this->loginAsAdmin();
        static::request('POST', '/api/users',
            json: [
                'username' => 'Adria',
                'email' => 'adria@adria.com',
                'password' => hash('md5', '1234'),
                'admin' => true,
                'roles' => ['ROLE_USER', 'ROLE_ADMIN']
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );
        $this->assertResponseStatusCodeSame(201);

        static::request('GET', '/api/users');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'username' => 'adri',
                    'email' => 'adria@test.com',
                    'admin' => true,
                    'roles' => ['ROLE_USER']
                ],
                [
                    'username' => 'Adria',
                    'email' => 'adria@adria.com',
                    'admin' => true,
                    'roles' => ['ROLE_USER', 'ROLE_ADMIN']
                ]
            ]
        ]);
    }
}
