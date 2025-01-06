<?php

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\AnswerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation;

use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ApiResource(operations: [
    // Disabling GET operation but can still be identified by an IRI
    new Get(
        controller: NotFoundAction::class,
        read: false,
        output: false
    ),
])]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Annotation\Groups([
        'FailedQuestion:V$List',
        'Question:V$List',
        'Quiz:W$Create',
    ])]
    private int $id;

    #[ORM\Column(type: Types::TEXT)]
    #[Annotation\Groups([
        'FailedQuestion:V$List',
        'Question:V$AdminDetail',
        'Question:V$List',
        'Quiz:V$Detail',
        'Question:W$Create',
        'Question:W$Update',
        'Quiz:W$Create',
    ])]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Annotation\Groups([
        'FailedQuestion:V$List',
        'Question:V$AdminDetail',
        'Question:V$List',
        'Quiz:V$Detail',
        'Question:W$Create',
        'Question:W$Update',
    ])]
    private bool $correct;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Annotation\Groups([
        'FailedQuestion:V$List',
        'Question:V$AdminDetail',
        'Question:V$List',
        'Quiz:V$Detail',
        'Question:W$Create',
        'Question:W$Update',
    ])]
    private ?string $explanation = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private ?Question $question = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

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

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): static
    {
        $this->correct = $correct;

        return $this;
    }

    public function getExplanation(): string
    {
        return $this->explanation;
    }

    public function setExplanation(string $explanation): static
    {
        $this->explanation = $explanation;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

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

    public function __construct()
    {
        $this->setCreatedAt(now());
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
