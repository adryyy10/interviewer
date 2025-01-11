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
    public const REF_REGULAR_USER = 'USER.REF_REGULAR_USER';
    public const REF_DEV_USER = 'USER.REF_DEV_USER';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        private readonly EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->addReference(self::REF_ADMIN_ADRI, $this->loadUser(
            username: 'adri',
            email: 'adria@test.com',
            password: '1234',
            isAdmin: true,
            apikey: 'thisisatestkey',
        ));

        $this->addReference(self::REF_REGULAR_USER, $this->loadUser(
            username: 'regular',
            email: 'regular.user@test.com',
            password: '1234',
            isAdmin: false,
            apikey: 'thisisatestkey2',
        ));

        $this->addReference(self::REF_DEV_USER, $this->loadUser(
            username: 'dev',
            email: 'adriafigueresgarciauk@gmail.com',
            password: '1234',
            isAdmin: false,
            apikey: 'thisisatestkey3',
        ));

        $this->em->flush();
    }

    private function loadUser(
        string $username,
        string $email,
        string $password,
        string $apikey,
        bool $isAdmin = false,
    ): User {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setAdmin($isAdmin);
        $user->apiKey = $apikey;

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        if ($isAdmin) {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        }

        $this->em->persist($user);

        return $user;
    }
}
