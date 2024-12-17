<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\FailedQuestion;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Webmozart\Assert\Assert;

class FailedQuestionExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private readonly Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (FailedQuestion::class !== $resourceClass) {
            return;
        }

        if (FailedQuestion::QUIZ_FAILED_QUESTIONS === $operation->getName()) {
            $user = $this->security->getUser();
            Assert::isInstanceOf($user, User::class);

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.createdBy = :current_user', $rootAlias))
                         ->setParameter('current_user', $user);
        }
    }
}
