<?php

namespace App\Tests;

use App\Entity\Question;

class FailedQuestionTest extends InterviewerTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateGetCollection(): void
    {
        $this->logInAsRegularUser();
        
        $phpQuestion = $this->findQuestionByContent('What does PHP stand for?');
        $question1Iri = $this->findIriBy(Question::class, ['id' => $phpQuestion->getId()]);

        static::request('POST', '/failed_questions',
            json: [
                'question' => $question1Iri
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );
        $this->assertResponseIsSuccessful();

        static::request('GET', '/failed_questions');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'hydra:member' => [
                [
                    'question' => [
                        "content" => "What does PHP stand for?",
                        "category" => "php"
                    ],
                ]
            ]
        ]);
    }

}
