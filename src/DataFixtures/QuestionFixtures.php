<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\User;
use App\Enum\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public const REF_PHP_VERSION = 'QUESTION.REF_PHP_VERSION';
    public const REF_PHP_STANDS = 'QUESTION.REF_PHP_STANDS';
    public const REF_JS_VERSION = 'QUESTION.REF_JS_VERSION';
    public const REF_NON_APPROVED = 'QUESTION.REF_NON_APPROVED';

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = $this->getReference(UserFixtures::REF_ADMIN_ADRI);
        Assert::isInstanceOf($adminUser, User::class);

        $this->addReference(self::REF_PHP_VERSION, $this->loadQuestion(
            'Which is the latest PHP version?',
            Category::PHP,
            $adminUser,
            approved: true,
        ));

        $this->addReference(self::REF_PHP_STANDS, $this->loadQuestion(
            'What does PHP stand for?',
            Category::PHP,
            $adminUser,
            approved: true,
        ));

        $this->addReference(self::REF_JS_VERSION, $this->loadQuestion(
            'Which ECMA version are we in?',
            Category::JS,
            $adminUser,
            approved: true,
        ));

        $this->addReference(self::REF_NON_APPROVED, $this->loadQuestion(
            'Is CircleCI useful for monitorising?',
            Category::Devops,
            $adminUser,
            approved: false,
        ));

        $this->em->flush();
    }

    private function loadQuestion(
        string $content,
        Category $category,
        User $createdBy,
        bool $approved = false,
    ): Question {
        $question = new Question();
        $question->setContent($content);
        $question->setCategory($category);
        $question->setCreatedBY($createdBy);
        $question->setApproved($approved);

        $this->em->persist($question);

        return $question;
    }
}
