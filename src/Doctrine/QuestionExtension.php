<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Question;
use Doctrine\ORM\QueryBuilder;

class QuestionExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (Question::class !== $resourceClass) {
            return;
        }

        if ($operation->getName() === Question::QUIZ_QUESTIONS) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.approved = :approved', $rootAlias))
                         ->setParameter('approved', true);
        }
    }
}
