<?php

namespace App\Provider\Question;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;

class QuestionsQuizProvider implements ProviderInterface
{

    public function __construct(
        public readonly EntityManagerInterface $em
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->em->getRepository(Question::class)->findQuestions(category: $context['filters']['category']);
    }
}
