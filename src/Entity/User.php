<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            uriTemplate: "/admin/users",
            normalizationContext: [
                'groups' => ['User:V$List']
            ]
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            uriTemplate: '/admin/users/{id}',
        ),
        new Post(
            uriTemplate: '/signup',
            denormalizationContext: [
                'groups' => ['User:W$Create']
            ]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            uriTemplate: "/admin/users/{id}"
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, unique: true)]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
        'User:V$List', 
        'User:W$Create'
    ])]
    private string $username;

    #[ORM\Column(length: 255)]
    #[Annotation\Groups(['User:V$List', 'User:W$Create'])]
    private string $email;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $apiKey = null;

    #[ORM\Column(length: 255)]
    #[Annotation\Groups(['User:V$List', 'User:W$Create'])]
    private string $password;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Annotation\Groups(['User:V$List', 'User:W$Create'])]
    private bool $admin = false;

    #[ORM\Column(type: Types::JSON)]
    #[Annotation\Groups(['User:V$List', 'User:W$Create'])]
    private array $roles = [];

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'createdBy', cascade: ['persist', 'remove'])]
    private Collection $questions;

    /**
     * @var Collection<int, Questionnaire>
     */
    #[ORM\OneToMany(targetEntity: Questionnaire::class, mappedBy: 'createdBy', cascade: ['persist', 'remove'])]
    private Collection $questionnaires;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function __construct()
    {
        $this->setCreatedAt(now());
        $this->roles = ['ROLE_USER'];
        $this->questions = new ArrayCollection();
        $this->questionnaires = new ArrayCollection();
    }
}
