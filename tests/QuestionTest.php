<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class QuestionTest extends ApiTestCase
{
    public function testListQuestions(): void
    {
        static::createClient()->request('GET', '/api/questions');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Question',
            '@id' => '/api/questions',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'content' => 'Which is the latest PHP version?',
                    'category' => 'PHP',
                ]
            ]
        ]);
    }

    public function testCreateQuestion(): void
    {
        static::createClient()->request('POST', '/api/questions', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'content' => 'Is PHP case sensitive?',
                'category' => 'PHP',
                'answers' => [
                    [
                        'content' => 'Yes',
                        'correct' => false,
                        'explanation' => 'Functions are key insensitive'
                    ],
                    [
                        'content' => 'No',
                        'correct' => false,
                        'explanation' => 'Variables are key sensitive'
                    ],
                    [
                        'content' => 'Partially',
                        'correct' => true,
                        'explanation' => 'Variables are key sensitive but functions and classes are key insensitive'
                    ]
                ]
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }
}
