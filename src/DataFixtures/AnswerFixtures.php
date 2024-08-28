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
        $phpQuestionVersion = $this->getReference(QuestionFixtures::REF_PHP_VERSION);
        Assert::isInstanceOf($phpQuestionVersion, Question::class);
        
        $this->loadAnswer(
            '7.4',
            $phpQuestionVersion,
            explanation: "PHP 7.4 was released November 28, 2019 and it's now End of Life, is no longer supported",
            isCorrect: false
        );

        $this->loadAnswer(
            '8.1',
            $phpQuestionVersion,
            explanation: "PHP 8.1 was released November 25, 2021 but it's not the latest one",
            isCorrect: false
        );

        $this->loadAnswer(
            '8.2',
            $phpQuestionVersion,
            explanation: "PHP 8.2 was released Decemeber 8, 2022 but it's not the latest one",
            isCorrect: false
        );

        $this->loadAnswer(
            content: '8.3',
            question: $phpQuestionVersion,
            explanation: "PHP 8.3 was released November 23, 2023 and it's the latest one",
            isCorrect: true
        );

        $phpQuestionStands = $this->getReference(QuestionFixtures::REF_PHP_STANDS);
        Assert::isInstanceOf($phpQuestionStands, Question::class);

        $this->loadAnswer(
            'Personal Home Page',
            $phpQuestionStands,
            explanation: "Incorrect",
            isCorrect: false
        );

        $this->loadAnswer(
            'PHP: Hypertext Preprocessor',
            $phpQuestionStands,
            isCorrect: true,
            explanation: "Correct!"

        );

        $this->loadAnswer(
            'Preprocessor Home Page',
            $phpQuestionStands,
            explanation: "Incorrect",
            isCorrect: false
        );

        $this->loadAnswer(
            'Personal Hypertext Processor',
            $phpQuestionStands,
            explanation: "Incorrect",
            isCorrect: false
        );

        $this->em->flush();
    }

    private function loadAnswer(
        string $content,
        Question $question,
        string $explanation,
        bool $isCorrect,
    ): Answer
    {
        $answer = new Answer();
        $answer->setContent($content);
        $answer->setQuestion($question);
        $answer->setExplanation($explanation);
        $answer->setCorrect($isCorrect);
        $answer->setCreatedAt(now());

        $this->em->persist($answer);

        return $answer;
    }

}
