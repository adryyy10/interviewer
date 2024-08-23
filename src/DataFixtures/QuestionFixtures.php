<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture
{
    public const REF_PHP = 'QUESTION.REF_PHP';

    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {}

    public function load(ObjectManager $manager)
    {
        $this->addReference(self::REF_PHP,$this->loadQuestion(
            'Which is the latest PHP version?',
            'PHP',
        ));

        $this->em->flush();
    }

    private function loadQuestion(
        string $content,
        string $category
    ): Question
    {
        $question = new Question();
        $question->setContent($content);
        $question->setCategory($category);

        $this->em->persist($question);

        return $question;
    }

}
