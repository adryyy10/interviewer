<?php

namespace App\Tests;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;
use Symfony\Component\HttpFoundation\Response;

class QuizTest extends InterviewerTestCase
{
    use ClockSensitiveTrait;

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
        static::mockTime(new \DateTimeImmutable('2022-03-02'));
        $response = static::request('POST', '/quizzes',
            json: [
                'punctuation' => 20,
                'categories' => ['js'],
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        )->toArray();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $createdIri = $response['@id'];

        static::mockTime(new \DateTimeImmutable('2024-03-02'));
        $response = static::request('POST', '/quizzes',
            json: [
                'punctuation' => 87,
                'categories' => ['php'],
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        )->toArray();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $createdIri = $response['@id'];

        // Admins can see others quizzes
        $this->logInAsAdmin();
        static::request('GET', $createdIri);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->logInAsRegularUser();
        static::request('GET', $createdIri);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                'punctuation' => 87,
                'categories' => ['php'],
            ]
        );

        static::request('GET', '/my-quizzes?order[createdAt]=desc');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "hydra:member" => [
                [
                    "punctuation" => 87,
                    'categories' => ['php'],
                ],
                [
                    "punctuation" => 20,
                    'categories' => ['js'],
                ],
            ],
        ]);
    }

    public function testCreateQuizWithQuestions(): void
    {
        $question1 = $this->findQuestionByContent('Which is the latest PHP version?');
        $answer1 = $this->findAnswerByContent('8.3');

        $question1Iri = $this->findIriBy(Question::class, ['id' => $question1->getId()]);
        $answer1Iri = $this->findIriBy(Answer::class, ['id' => $answer1->getId()]);

        $question2 = $this->findQuestionByContent('What does PHP stand for?');
        $answer2 = $this->findAnswerByContent('PHP: Hypertext Preprocessor');

        $question2Iri = $this->findIriBy(Question::class, ['id' => $question2->getId()]);
        $answer2Iri = $this->findIriBy(Answer::class, ['id' => $answer2->getId()]);

        $this->logInAsRegularUser();
        $response = static::request('POST', '/quizzes',
            json: [
                'punctuation' => 87,
                'categories' => ['php', 'js'],
                'userAnswers' => [
                    [
                        'question' => $question1Iri,
                        'answer' => $answer1Iri
                    ],
                    [
                        'question' => $question2Iri,
                        'answer' => $answer2Iri
                    ],
                ]
            ],
            headers: [
                'Content-Type' => 'application/ld+json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $res = $response->toArray();
        $createdIri = $res['@id'];

        $res = static::request('GET', $createdIri)->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                'punctuation' => 87,
                'categories' => ['php', 'js'],
                'userAnswers' => [
                    [
                        'question' => [
                            'content' => 'Which is the latest PHP version?',
                            'category' => 'php',
                        ],
                        'answer' => [
                            'content' => '8.3',
                            'correct' => true,
                            'explanation' => "PHP 8.3 was released November 23, 2023 and it's the latest one"
                        ],
                    ],
                    [
                        'question' => [
                            'content' => 'What does PHP stand for?',
                            'category' => 'php',
                        ],
                        'answer' => [
                            'content' => 'PHP: Hypertext Preprocessor',
                            'correct' => true,
                            'explanation' => "Correct!"
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
