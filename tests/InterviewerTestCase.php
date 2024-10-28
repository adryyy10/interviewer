<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

class InterviewerTestCase extends ApiTestCase
{
    protected static ?string $myAuthToken = null;
    protected static ?string $myEmail = null;
    protected static ?string $myId = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        static::bootKernel();
        $this->resetDatabase();
    }

    #[\Override]
    protected function tearDown(): void
    {
        static::$myAuthToken = null;
        static::$myEmail = null;
        static::$myId = null;
        parent::tearDown();
    }

    protected function resetDatabase(): void
    {
        $process = new Process(['sh', './reset_test_db.sh']);
        $process->mustRun();
    }


    protected function getEm(): EntityManagerInterface
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        Assert::isInstanceOf($em, EntityManagerInterface::class);
        return $em;
    }

    protected function logInAsAdmin(): void
    {
        $this->logIn('adria@test.com', '1234');
    }

    protected function logInAsRegularUser(): void
    {
        $this->logIn('regular.user@test.com', '1234');
    }

    public function logOut(): void
    {
        static::$myAuthToken = null;
        static::$myEmail = null;
        static::$myId = null;
    }

    /**
    * Perform an API request with automatically managed authentication headers and the option to include custom headers.
    */
    protected static function request(string $method, string $url, mixed $json = null, mixed $headers = []): ResponseInterface
    {
        $headers += ['Authorization' => 'Basic '.base64_encode(static::$myAuthToken)];
        if ('PATCH' === $method) {
            $headers += ['Content-Type' => 'application/merge-patch+json'];
        }

        $response = static::createClient()
            ->request($method, $url, options: ['headers' => $headers, 'json' => $json ?? []]);

        $response->getHeaders(throw: false);

        return $response;
    }

    private function logIn(string $email, string $password): void
    {
        $res = static::createClient()
            ->request('GET', '/auth/lookup', [
                'headers' => [
                    'Authorization' => 'Basic '.base64_encode("{$email}:{$password}"),
                ],
            ])->toArray();

        $this->assertIsString($res['apiKey']);
        $this->assertNotEmpty($res['email']);

        static::$myAuthToken = $res['apiKey'];
        static::$myEmail = $res['email'];
        static::$myId = $res['id'];
    }

    protected function findQuestionByContent(string $content): Question
    {
        $question = $this->getEm()->getRepository(Question::class)->findOneBy(['content' => $content]);
        Assert::isInstanceOf($question, Question::class);

        return $question;
    }

    protected function findAnswerByContent(string $content): Answer
    {
        $answer = $this->getEm()->getRepository(Answer::class)->findOneBy(['content' => $content]);
        Assert::isInstanceOf($answer, Answer::class);

        return $answer;
    }
}