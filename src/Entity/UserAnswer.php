<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class UserAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'Quiz:V$Detail',
    ])]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'userAnswers')]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'Quiz:V$Detail',
        'Quiz:W$Create',
    ])]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Question $question;

    #[ORM\ManyToOne(targetEntity: Answer::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'Quiz:V$Detail',
        'Quiz:W$Create',
    ])]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Answer $answer;

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
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

    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    public function setAnswer(Answer $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
