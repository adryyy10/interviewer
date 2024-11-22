<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enum\Category;
use App\Interface\CreatableByUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('ROLE_USER')",
            denormalizationContext: [
                'groups' => ['Quiz:W$Create'],
            ],
            normalizationContext: [
                'groups' => [
                    'Quiz:V$Detail',
                ],
            ]
        ),
        new Get(
            security: "user == object.getCreatedBy() or is_granted('ROLE_ADMIN')",
            normalizationContext: [
                'groups' => [
                    'Quiz:V$Detail',
                ],
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
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
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
        'Quiz:V$Detail',
        'Quiz:V$List',
        'Quiz:W$Create',
    ])]
    private int $punctuation;

    #[ORM\Column]
    #[Groups([
        'Quiz:V$Detail',
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
        'Quiz:V$Detail',
        'Quiz:V$List',
        'Quiz:W$Create',
    ])]
    private ?string $remarks = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'Quiz:V$Detail',
        'Quiz:V$List',
        'Quiz:W$Create',
    ])]
    private Category $category;

    /**
     * @var Collection<int, UserAnswer>
     */
    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: UserAnswer::class, cascade: ['persist', 'remove'])]
    #[Groups([
        'Quiz:V$Detail',
        'Quiz:W$Create',
    ])]
    #[ApiProperty(push: true)]
    #[Assert\Valid]
    private Collection $userAnswers;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->userAnswers = new ArrayCollection();
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

    /**
     * @return Collection<int, UserAnswer>
     */
    public function getUserAnswers(): Collection
    {
        return $this->userAnswers;
    }

    public function addUserAnswer(UserAnswer $userAnswer): static
    {
        if (!$this->userAnswers->contains($userAnswer)) {
            $this->userAnswers->add($userAnswer);
            $userAnswer->setQuiz($this);
        }

        return $this;
    }

    public function removeUserAnswer(UserAnswer $userAnswer): static
    {
        if ($this->userAnswers->removeElement($userAnswer)) {
            // set the owning side to null (unless already changed)
            if ($userAnswer->getQuiz() === $this) {
                $userAnswer->setQuiz(null);
            }
        }

        return $this;
    }
}
