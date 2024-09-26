<?php

namespace App\Tests;

class QuestionnaireTest extends InterviewerTestCase
{
    public function testCreate(): void
    {
        // No login -> 403
        static::request('POST', '/questionnaires',
            json: [
                'punctuation' => 87,
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsRegularUser();
        static::request('POST', '/questionnaires',
            json: [
                'punctuation' => 87,
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );
        $this->assertResponseStatusCodeSame(201);
    }
}
