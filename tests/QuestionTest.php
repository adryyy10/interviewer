<?php

namespace App\Tests;

class QuestionTest extends InterviewerTestCase
{
    public function testListQuestions(): void
    {
        $res = static::createClient()->request('GET', '/questions')->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertTrue($this->isInList($res, 'Which is the latest PHP version?'));

        // category filter
        $res = static::createClient()->request('GET', '/questions?category=JS')->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertFalse($this->isInList($res, 'Which is the latest PHP version?'));
        $this->assertTrue($this->isInList($res, 'Which ECMA version are we in?'));

        // approved filter
        $res = static::createClient()->request('GET', '/questions?approved=true')->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertTrue($this->isInList($res, 'Which ECMA version are we in?'));
        $this->assertFalse($this->isInList($res, 'Is CircleCI useful for monitorising?'));
    }

    public function testCreateQuestion(): void
    {
        // No login -> 403
        static::request('POST', '/admin/questions', json: [
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

        // Logged as regular user -> 403
        $this->logInAsRegularUser();
        static::request('POST', '/admin/questions', json: [
            'content' => 'Is PHP case sensitive?',
            'category' => 'php',
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
        $res = static::request('POST', '/admin/questions', json: [
            'content' => 'Is PHP case sensitive?',
            'category' => 'php',
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
        ])->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $questionIri = $res['@id'];

        $res = static::request('GET', $questionIri)->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "content" => "Is PHP case sensitive?",
            "category" => "php",
            "createdBy" => [
              "username" => "adri",
            ],
            "approved" => false,
        ]);

    }

    public function testListAdminQuestions(): void
    {
        // No login -> 403
        static::request('GET', '/admin/questions');
        $this->assertResponseStatusCodeSame(403);

        // Logged as regular user -> 403
        $this->logInAsRegularUser();
        static::request('GET', '/admin/questions');
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', '/admin/questions');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/contexts/Question',
            '@id' => '/admin/questions',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'content' => 'Which is the latest PHP version?',
                    'category' => 'php',
                    'createdBy' => [
                        'username' => 'adri',
                    ],
                    'approved' => true,
                ]
            ]
        ]);
    }

    public function testGetQuestion(): void
    {
        $question = $this->findQuestionByContent('Which is the latest PHP version?');

        // No login -> 403
        static::request('GET', "/admin/questions/{$question->getId()}");
        $this->assertResponseStatusCodeSame(403);

        // Logged as regular user -> 403
        $this->logInAsRegularUser();
        static::request('GET', "/admin/questions/{$question->getId()}");
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('GET', "/admin/questions/{$question->getId()}");
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteQuestion(): void
    {
        $question = $this->findQuestionByContent('Which is the latest PHP version?');

        // No login -> 403
        static::request('DELETE', "/admin/questions/{$question->getId()}");
        $this->assertResponseStatusCodeSame(403);

        // Logged as regular user -> 403
        $this->logInAsRegularUser();
        static::request('DELETE', "/admin/questions/{$question->getId()}");
        $this->assertResponseStatusCodeSame(403);

        $this->logInAsAdmin();
        static::request('DELETE', "/admin/questions/{$question->getId()}");
        $this->assertResponseIsSuccessful();
    }

    private function isInList(array $response, string $questionContent): bool
    {
        foreach($response['hydra:member'] as $question) {
            if ($question['content'] === $questionContent) {
                return true;
            }
        }
        return false;
    }
}
