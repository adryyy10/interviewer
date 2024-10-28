<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enum\Category;
use App\Interface\CreatableByUserInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('ROLE_USER')",
            denormalizationContext: [
                'groups' => ['Quiz:W$Create'],
            ]
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            uriTemplate: '/admin/quizzes',
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            name: self::MY_QUIZZES,
            uriTemplate: '/my-quizzes',
            normalizationContext: [
                'groups' => [
                    'Quiz:V$List',
                ],
            ]
        ),
    ]
)]
class Quiz implements CreatableByUserInterface
{
    public const MY_QUIZZES = 'my-quizzes';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'Quiz:V$List',
    ])]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups([
        'Quiz:V$List',
        'Quiz:W$Create',
    ])]
    private int $punctuation;

    #[ORM\Column]
    #[Groups([
        'Quiz:V$List',
    ])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'Quiz:V$List',
    ])]
    private User $createdBy;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'Quiz:V$List',
        'Quiz:W$Create',
    ])]
    private ?string $remarks = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'Quiz:V$List',
        'Quiz:W$Create',
    ])]
    private Category $category;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPunctuation(): int
    {
        return $this->punctuation;
    }

    public function setPunctuation(int $punctuation): self
    {
        $this->punctuation = $punctuation;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category->value;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
