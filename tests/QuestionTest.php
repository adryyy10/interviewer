<?php

namespace App\Tests;

class QuestionTest extends InterviewerTestCase
{
    public function testListQuestions(): void
    {
        $this->logInAsAdmin();
        static::request('GET', '/api/questions');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Question',
            '@id' => '/api/questions',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'content' => 'Which is the latest PHP version?',
                    'category' => 'PHP',
                    'answers' => [
                        [
                            'content' => '7.4',
                            'correct' => false,
                            'explanation' => 'PHP 7.4 was released November 28, 2019 and it\'s now End of Life, is no longer supported',
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testCreateQuestion(): void
    {
        // No login -> 403
        static::request('POST', '/api/questions', json: [
            'content' => 'Is PHP case sensitive?',
            'category' => 'PHP',
            'answers' => [
                [
                    'content' => 'Yes',
                    'correct' => false,
                    'explanation' => 'Functions are case insensitive'
                ],
                [
                    'content' => 'No',
                    'correct' => false,
                    'explanation' => 'Variables are case sensitive'
                ],
                [
                    'content' => 'Partially',
                    'correct' => true,
                    'explanation' => 'Variables are case sensitive but functions and classes are case insensitive'
                ]
            ]
        ], headers: [
            'Content-Type' => 'application/ld+json',
        ]);
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('POST', '/api/questions', json: [
            'content' => 'Is PHP case sensitive?',
            'category' => 'PHP',
            'answers' => [
                [
                    'content' => 'Yes',
                    'correct' => false,
                    'explanation' => 'Functions are case insensitive'
                ],
                [
                    'content' => 'No',
                    'correct' => false,
                    'explanation' => 'Variables are case sensitive'
                ],
                [
                    'content' => 'Partially',
                    'correct' => true,
                    'explanation' => 'Variables are case sensitive but functions and classes are case insensitive'
                ]
            ]
        ], headers: [
            'Content-Type' => 'application/ld+json',
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testListAdminQuestions(): void
    {
        static::request('GET', '/api/admin/questions');
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', '/api/admin/questions');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Question',
            '@id' => '/api/admin/questions',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'content' => 'Which is the latest PHP version?',
                    'category' => 'PHP',
                    'createdBy' => [
                        'username' => 'adri',
                    ],
                ]
            ]
        ]);
    }
}
