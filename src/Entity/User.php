<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation;

use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => [
                    'User:V$List'
                ]
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => [
                    'User:W$Create'
                ]
            ]
        )
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Annotation\Groups([
        'User:V$List',
        'User:W$Create'
    ])]
    private string $name;

    #[ORM\Column(length: 255)]
    #[Annotation\Groups([
        'User:V$List',
        'User:W$Create'
    ])]
    private string $email;

    #[ORM\Column(length: 255)]
    #[Annotation\Groups([
        'User:V$List',
        'User:W$Create'
    ])]
    private string $password;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Annotation\Groups([
        'User:V$List',
        'User:W$Create'
    ])]
    private bool $admin;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    public function __construct()
    {
        $this->setCreatedAt(now());
    }
}
