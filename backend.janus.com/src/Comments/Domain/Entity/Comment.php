<?php

declare(strict_types=1);

namespace App\Comments\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'comments')]
class Comment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 200)]
    private string $collection;

    #[ORM\Column(length: 255)]
    private string $item;

    #[ORM\Column(type: 'text')]
    private string $comment;

    /** UUID of the user who wrote this comment — plain string, no ORM FK */
    #[ORM\Column(length: 36)]
    private string $userId;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        string $collection,
        string $item,
        string $comment,
        string $userId,
    ) {
        $this->id         = Uuid::v7();
        $this->collection = $collection;
        $this->item       = $item;
        $this->comment    = $comment;
        $this->userId     = $userId;
        $this->createdAt  = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getCollection(): string { return $this->collection; }
    public function getItem(): string { return $this->item; }
    public function getUserId(): string { return $this->userId; }

    public function getComment(): string { return $this->comment; }
    public function setComment(string $comment): static
    {
        $this->comment   = $comment;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function isOwnedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }
}
