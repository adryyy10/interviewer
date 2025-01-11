<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Tries sequentially the several ways we may want to be looking up a user.
 */
/**
 * @implements UserProviderInterface<User>
 */
final class InterviewerUserProvider implements UserProviderInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadUserByIdentifier(string $email): UserInterface
    {
        $userRepo = $this->em->getRepository(User::class);

        // 1: AccountUser email address
        /** @var User|null $user */
        $user = $userRepo->findOneBy(['email' => strtolower($email)]);
        if ($user) {
            return $user;
        }

        // 2: AccountUser api key
        /** @var User|null $user */
        $user = $userRepo->findOneBy(['apiKey' => $email]);
        if ($user) {
            return $user;
        }

        throw new UserNotFoundException(sprintf('User "%s" not found.', $email));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if ($user instanceof User) {
            return $this->em->getRepository(User::class)
                ->find($user->getId());
        }
        throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
