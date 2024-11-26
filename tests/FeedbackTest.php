<?php

namespace App\Tests;

class FeedbackTest extends InterviewerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateGetCollection(): void
    {
        // No login -> 403
        static::request('POST', '/feedback', json: [
            'content' => 'Could you please create live coding questions?',
        ], headers: [
            'Content-Type' => 'application/ld+json',
        ]);
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsRegularUser();
        static::request('POST', '/feedback', json: [
            'content' => 'Could you please create live coding questions?',
        ], headers: [
            'Content-Type' => 'application/ld+json',
        ]);
        $this->assertResponseIsSuccessful();

        // Logged as regular user -> 403
        static::request('GET', '/admin/feedback');
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', '/admin/feedback');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Feedback',
            '@id' => '/admin/feedback',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'content' => 'Could you please create live coding questions?',
                    'createdBy' => [
                        'username' => 'regular',
                        'email' => 'regular.user@test.com',
                    ]
                ]
            ]
        ]);
    }
}
