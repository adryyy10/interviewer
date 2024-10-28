<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class UserAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'userAnswers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'Quiz:V$Detail',
        'Quiz:W$Create'
    ])]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Question $question;

    #[ORM\ManyToOne(targetEntity: Answer::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'Quiz:V$Detail',
        'Quiz:W$Create'
    ])]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Answer $selectedAnswer;

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
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

    public function getSelectedAnswer(): Answer
    {
        return $this->selectedAnswer;
    }

    public function setSelectedAnswer(Answer $selectedAnswer): self
    {
        $this->selectedAnswer = $selectedAnswer;

        return $this;
    }
}
