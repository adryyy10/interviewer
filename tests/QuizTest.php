<?php

namespace App\Tests;

class QuizTest extends InterviewerTestCase
{
    public function testCreateGetMyQuizzes(): void
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

        static::request('GET', '/my-quizzes');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "hydra:member" => [
                [
                    "punctuation" => 87,
                ]
            ],
        ]);
    }

    public function testAdminQuizzes(): void
    {
        // No login -> 403
        static::request('GET', '/admin/quizzes');
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', '/admin/quizzes');
        $this->assertResponseIsSuccessful();
    }
}
