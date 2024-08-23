<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

use function Symfony\Component\Clock\now;

class AnswerFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {}

    public function getDependencies()
    {
        return [
            QuestionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $phpQuestion = $this->getReference(QuestionFixtures::REF_PHP);
        Assert::isInstanceOf($phpQuestion, Question::class);
        
        $this->loadAnswer(
            '7.4',
            $phpQuestion,
        );

        $this->loadAnswer(
            '8.1',
            $phpQuestion,
        );

        $this->loadAnswer(
            '8.2',
            $phpQuestion,
        );

        $this->loadAnswer(
            content: '8.3',
            question: $phpQuestion,
            isCorrect: true
        );

        $this->em->flush();
    }

    private function loadAnswer(
        string $content,
        Question $question,
        ?bool $isCorrect = false,
    ): Answer
    {
        $answer = new Answer();
        $answer->setContent($content);
        $answer->setQuestion($question);
        $answer->setCorrect($isCorrect);
        $answer->setCreatedAt(now());

        $this->em->persist($answer);

        return $answer;
    }

}
