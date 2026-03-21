<?php

declare(strict_types=1);

namespace App\Shares\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'shares')]
class Share
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** Opaque token used in the public URL */
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $token;

    #[ORM\Column(type: 'string', length: 64)]
    private string $collection;

    #[ORM\Column(type: 'string', length: 255)]
    private string $item;

    /** User who created the share link */
    #[ORM\Column(type: 'string', length: 36)]
    private string $userId;

    /** Optional human-readable label */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    /** Optional password to protect the share */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $password;

    /** Optional expiry — null means never expires */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt;

    /** Maximum number of uses — null means unlimited */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxUses;

    #[ORM\Column(type: 'integer')]
    private int $timesUsed = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string  $collection,
        string  $item,
        string  $userId,
        string  $token,
        ?string $name      = null,
        ?string $password  = null,
        ?\DateTimeImmutable $expiresAt = null,
        ?int    $maxUses   = null,
    ) {
        $this->id         = Uuid::v7()->toRfc4122();
        $this->collection = $collection;
        $this->item       = $item;
        $this->userId     = $userId;
        $this->token      = $token;
        $this->name       = $name;
        $this->password   = $password;
        $this->expiresAt  = $expiresAt;
        $this->maxUses    = $maxUses;
        $this->timesUsed  = 0;
        $this->createdAt  = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getToken(): string { return $this->token; }
    public function getCollection(): string { return $this->collection; }
    public function getItem(): string { return $this->item; }
    public function getUserId(): string { return $this->userId; }
    public function getName(): ?string { return $this->name; }
    public function getPassword(): ?string { return $this->password; }
    public function getExpiresAt(): ?\DateTimeImmutable { return $this->expiresAt; }
    public function getMaxUses(): ?int { return $this->maxUses; }
    public function getTimesUsed(): int { return $this->timesUsed; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt < new \DateTimeImmutable();
    }

    public function isExhausted(): bool
    {
        return $this->maxUses !== null && $this->timesUsed >= $this->maxUses;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isExhausted();
    }

    public function recordUse(): static
    {
        $this->timesUsed++;
        return $this;
    }

    public function isOwnedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }
}
