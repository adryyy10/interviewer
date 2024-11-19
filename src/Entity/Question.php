<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\Category;
use App\Interface\CreatableByUserInterface;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation;

use function Symfony\Component\Clock\now;

#[ApiResource(
    operations: [
        new GetCollection(
            name: self::APPROVED_QUESTIONS,
            normalizationContext: [
                'groups' => [
                    'Question:V$List',
                ],
            ]
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            uriTemplate: '/admin/questions',
            normalizationContext: [
                'groups' => [
                    'Question:V$AdminList',
                ],
            ]
        ),
        new Get(
            uriTemplate: '/admin/questions/{id}',
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: [
                'groups' => [
                    'Question:V$AdminDetail',
                ],
            ]
        ),
        new Patch(
            uriTemplate: '/admin/questions/{id}',
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: [
                'groups' => [
                    'Question:W$Update',
                ],
            ],
            normalizationContext: [
                'groups' => [
                    'Question:V$AdminDetail',
                ],
            ],
        ),
        new Post(
            uriTemplate: '/admin/questions',
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: [
                'groups' => [
                    'Question:W$Create',
                ],
                'disable_type_enforcement' => true,
            ],
        ),
        new Delete(
            uriTemplate: '/admin/questions/{id}',
            security: "is_granted('ROLE_ADMIN')",
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'iexact'])]
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question implements CreatableByUserInterface
{
    public const APPROVED_QUESTIONS = 'approved-questions';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
        'Question:V$List',
    ])]
    private int $id;

    #[ORM\Column(type: Types::TEXT)]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
        'Question:V$List',
        'Quiz:V$Detail',
        'Question:W$Create',
        'Question:W$Update',
    ])]
    private string $content;

    #[ORM\Column(length: 255)]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
        'Question:V$List',
        'Quiz:V$Detail',
        'Question:W$Create',
        'Question:W$Update',
    ])]
    private Category $category;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
    ])]
    private User $createdBy;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
    ])]
    private User $updatedBy;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Annotation\Groups([
        'Question:V$AdminDetail',
        'Question:V$AdminList',
        'Question:W$Create',
        'Question:W$Update',
    ])]
    private bool $approved = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist', 'remove'])]
    #[Annotation\Groups([
        'Question:V$List',
        'Question:W$Create',
    ])]
    private Collection $answers;

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): static
    {
        $this->approved = $approved;

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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __construct()
    {
        $this->setCreatedAt(now());
        $this->answers = new ArrayCollection();
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
