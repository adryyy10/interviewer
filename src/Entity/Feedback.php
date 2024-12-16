<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Interface\CreatableByUserInterface;
use App\Repository\FeedbackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation;

use function Symfony\Component\Clock\now;

#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            uriTemplate: '/admin/feedback',
            normalizationContext: [
                'groups' => [
                    'Feedback:V$AdminList',
                ],
            ],
        ),
        new Get(
            uriTemplate: '/admin/feedback/{id}',
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: [
                'groups' => [
                    'Feedback:V$AdminDetail',
                ],
            ],
        ),
        new Post(
            uriTemplate: '/feedback',
            security: "is_granted('ROLE_USER')",
            denormalizationContext: [
                'groups' => [
                    'Feedback:W$Create',
                ],
            ],
        ),
    ]
)]
#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback implements CreatableByUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Annotation\Groups([
        'Feedback:V$AdminList',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Annotation\Groups([
        'Feedback:V$AdminDetail',
        'Feedback:V$AdminList',
        'Feedback:W$Create',
    ])]
    private ?string $content = null;

    #[ORM\Column]
    #[Annotation\Groups([
        'Feedback:V$AdminDetail',
        'Feedback:V$AdminList',
    ])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Annotation\Groups([
        'Feedback:V$AdminDetail',
        'Feedback:V$AdminList',
    ])]
    private User $createdBy;

    public function __construct()
    {
        $this->setCreatedAt(now());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
