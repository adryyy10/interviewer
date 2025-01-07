<?php

namespace App\Tests;

use App\Entity\Question;

class FailedQuestionTest extends InterviewerTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateUpdateGetCollection(): void
    {
        $this->logInAsRegularUser();
        
        $phpQuestion = $this->findQuestionByContent('What does PHP stand for?');
        $question1Iri = $this->findIriBy(Question::class, ['id' => $phpQuestion->getId()]);

        $response = static::request('POST', '/failed_questions',
            json: [
                'question' => $question1Iri
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        )->toArray();
        $createdIri = $response['@id'];
        $this->assertResponseIsSuccessful();

        static::request('GET', '/failed_questions');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'hydra:member' => [
                [
                    'question' => [
                        "content" => "What does PHP stand for?",
                        "category" => "php",
                        "answers" => [
                            [
                                "content" => "Personal Home Page",
                                "correct" => false,
                                "explanation" => "Incorrect",
                            ]
                        ]
                    ],
                ]
            ]
        ]);

        static::request('PATCH', $createdIri, json: [
            'correctlyAnswered' => true
        ], headers: [
            'Content-Type' => 'application/merge-patch+json',
        ]);
        $this->assertResponseIsSuccessful();

        // Returns empty array because the question is correctly answered
        static::request('GET', '/failed_questions');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'hydra:totalItems' => 0,
        ]);
    }

}
