<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Find questions with an optional category filter and shuffle the results.
     *
     * @param int         $limit    the maximum number of questions to return
     * @param string|null $category to filter questions by (optional)
     *
     * @return Question[] the shuffled list of questions
     */
    public function findQuestions(int $limit = 10, ?string $category = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->setMaxResults($limit);

        if ($category) {
            $qb->andWhere('q.category = :category')
                ->setParameter('category', $category);
        }

        $qb->andWhere('q.approved = true');

        $questions = $qb->getQuery()->getResult();

        shuffle($questions);

        return $questions;
    }
}
