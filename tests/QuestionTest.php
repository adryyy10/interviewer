<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class QuestionTest extends ApiTestCase
{
    public function testListQuestions(): void
    {
        $response = self::createClient()->request('GET', '/api/questions');
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
}
