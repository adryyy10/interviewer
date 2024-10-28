<?php

namespace App\Tests;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Component\HttpFoundation\Response;

class QuizTest extends InterviewerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

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
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->logInAsRegularUser();
        static::request('POST', '/quizzes',
            json: [
                'punctuation' => 87,
                'category' => 'php',
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        )->toArray();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        static::request('GET', '/my-quizzes');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "hydra:member" => [
                [
                    "punctuation" => 87,
                    'category' => 'php',
                ]
            ],
        ]);
    }

    public function testCreateQuizWithQuestions(): void
    {
        $question1 = $this->findQuestionByContent('Which is the latest PHP version?');
        $answer1 = $this->findAnswerByContent('8.3');

        $question1Iri = $this->findIriBy(Question::class, ['id' => $question1->getId()]);
        $answer1Iri = $this->findIriBy(Answer::class, ['id' => $answer1->getId()]);


        $this->logInAsRegularUser();
        $res = static::request('POST', '/quizzes',
            json: [
                'punctuation' => 87,
                'category' => 'php',
                'userAnswers' => [
                    [
                        'question' => $question1Iri,
                        'selectedAnswer' => $answer1Iri
                    ],
                ]
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        )->toArray();
        $createdIri = $res['@id'];
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $res = static::request('GET', $createdIri)->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                "punctuation" => 87,
                'category' => 'php',
                'userAnswers' => [
                    [
                        'question' => [
                            'content' => 'Which is the latest PHP version?',
                            'category' => 'php',
                        ],
                        'selectedAnswer' => [
                            'content' => '8.3',
                        ],
                    ],
                ]
            ]
        );
    }

    public function testAdminQuizzes(): void
    {
        // No login -> 403
        static::request('GET', '/admin/quizzes');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->logInAsAdmin();
        static::request('GET', '/admin/quizzes');
        $this->assertResponseIsSuccessful();
    }
}
