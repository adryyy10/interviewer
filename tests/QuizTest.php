<?php

namespace App\Tests;

class QuizTest extends InterviewerTestCase
{
    public function testCreate(): void
    {
        // No login -> 403
        static::request('POST', '/quizzes',
            json: [
                'punctuation' => 87,
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsRegularUser();
        static::request('POST', '/quizzes',
            json: [
                'punctuation' => 87,
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        )->toArray();
        $this->assertResponseStatusCodeSame(201);
    }
}
