<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Interface\CreatableByUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            normalizationContext: [
                'groups' => ['FailedQuestion:V$List']
            ]
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            denormalizationContext: [
                'groups' => ['FailedQuestion:W$Create']
            ]
        ),
    ]
)]
class FailedQuestion implements CreatableByUserInterface
{
    public const QUIZ_FAILED_QUESTIONS = 'quiz_failed_questions';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['FailedQuestion:V$List'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[Groups(['FailedQuestion:V$List', 'FailedQuestion:W$Create'])]
    private Question $question;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['FailedQuestion:V$List'])]
    private User $createdBy;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['FailedQuestion:V$List'])]
    private \DateTimeImmutable $failedAt;

    public function __construct()
    {
        $this->failedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;
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

    public function getFailedAt(): \DateTimeImmutable
    {
        return $this->failedAt;
    }
}
