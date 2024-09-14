<?php

namespace App\Tests;

use App\Entity\User;
use Webmozart\Assert\Assert;

class UserTest extends InterviewerTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetUsers(): void
    {
        // No login -> 403
        static::request('GET', '/api/admin/users');
        $this->assertResponseStatusCodeSame(403);

        // Login as regular user -> 403
        $this->logInAsAdminRegularUser();
        static::request('GET', '/api/admin/users');
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', '/api/admin/users');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/admin/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'username' => 'adri',
                    'email' => 'adria@test.com',
                    'admin' => true,
                    'roles' => ['ROLE_USER', 'ROLE_ADMIN']
                ],
                [
                    'username' => 'regular',
                    'email' => 'regular.user@test.com',
                    'admin' => false,
                    'roles' => ['ROLE_USER']
                ]
            ]
        ]);
    }

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
    }

    public function testDeleteUser(): void
    {
        $user = $this->getEm()->getRepository(User::class)->find(1);
        Assert::isInstanceOf($user, User::class);

        // No login -> 403
        static::request('DELETE', "/api/admin/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(403);

        // Login as regular user -> 403
        $this->logInAsAdminRegularUser();
        static::request('DELETE', "/api/admin/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('DELETE', "/api/admin/users/{$user->getId()}");
        $this->assertResponseIsSuccessful();
    }
}
