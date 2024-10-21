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
        static::request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(403);

        // Login as regular user -> 403
        $this->logInAsRegularUser();
        static::request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', '/admin/users');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/admin/users',
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

    public function testCreate(): void
    {
        static::request('POST', '/signup',
            json: [
                'username' => 'Adria',
                'email' => 'adria@adria.com',
                'password' => '1234',
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateUser(): void
    {
        $user = $this->getEm()->getRepository(User::class)->find(1);
        Assert::isInstanceOf($user, User::class);

        $this->logInAsAdmin();
        static::request('GET', "/admin/users/{$user->getId()}");
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "username" => "adri",
            "email" => "adria@test.com",
            "admin" => true,
        ]);
        
        static::request(
            'PATCH', 
            "/admin/users/{$user->getId()}", 
            [
                'username' => 'modifiedAdri', 
                'email' => 'adriamodified@test.com', 
                'admin' => false,
            ]
        );
        $this->assertResponseIsSuccessful();

        static::request('GET', "/admin/users/{$user->getId()}");
        $this->assertJsonContains([
            "username" => "modifiedAdri",
            "email" => "adriamodified@test.com",
            "admin" => false,
        ]);
    }

    public function testGetUser(): void
    {
        $user = $this->getEm()->getRepository(User::class)->find(1);
        Assert::isInstanceOf($user, User::class);

        // No login -> 403
        static::request('GET', "/admin/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(403);

        // Login as regular user -> 403
        $this->logInAsRegularUser();
        static::request('GET', "/admin/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', "/admin/users/{$user->getId()}");
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteUser(): void
    {
        $user = $this->getEm()->getRepository(User::class)->find(2);
        Assert::isInstanceOf($user, User::class);

        // No login -> 403
        static::request('DELETE', "/admin/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(403);

        // Login as regular user -> 403
        $this->logInAsRegularUser();
        static::request('DELETE', "/admin/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('DELETE', "/admin/users/{$user->getId()}");
        $this->assertResponseIsSuccessful();
    }
}
