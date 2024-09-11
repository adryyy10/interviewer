<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const REF_ADMIN_ADRI = 'USER.REF_ADMIN_ADRI';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        private readonly EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher // Inject the password hasher
    )
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->loadUser(
            username: 'adri',
            email: 'adria@test.com',
            password: '1234',
            isAdmin: true,
            apikey: 'thisisatestkey',
        );

        $this->em->flush();
    }

    private function loadUser(
        string $username,
        string $email,
        string $password,
        bool $isAdmin = false,
        string $apikey,
    ): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setAdmin($isAdmin);
        $user->apiKey = $apikey;

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        return $user;
    }

}