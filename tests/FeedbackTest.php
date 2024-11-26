<?php

namespace App\Tests;

class FeedbackTest extends InterviewerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreate(): void
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
    }
}
