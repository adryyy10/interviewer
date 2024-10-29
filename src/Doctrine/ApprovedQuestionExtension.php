<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Question;
use Doctrine\ORM\QueryBuilder;

class ApprovedQuestionExtension implements QueryCollectionExtensionInterface
{
    public function __construct()
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        // Only apply to Question entity and 'approved-questions' operation
        if (Question::class !== $resourceClass || Question::APPROVED_QUESTIONS !== $operation->getName()) {
            return;
        }

        $rootAliases = $queryBuilder->getRootAliases();
        $rootAlias = $rootAliases[0];

        $queryBuilder->andWhere(sprintf('%s.approved = :approved', $rootAlias))
           ->setParameter('approved', true);
    }
}
