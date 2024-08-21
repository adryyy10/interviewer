<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture
{

    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {}

    public function load(ObjectManager $manager)
    {
        $this->loadQuestion(
            'Which is the latest PHP version?',
            'PHP',
        );

        $this->em->flush();
    }

    private function loadQuestion(
        string $content,
        string $category
    ): void
    {
        $question = new Question();
        $question->setContent($content);
        $question->setCategory($category);

        $this->em->persist($question);
    }

}
