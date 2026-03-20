<?php

declare(strict_types=1);

namespace App\Notifications\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** Recipient user ID */
    #[ORM\Column(type: 'string', length: 36)]
    private string $recipientId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $subject;

    #[ORM\Column(type: 'text')]
    private string $message;

    /** Sender user ID — null for system-generated notifications */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $senderId;

    /** Optional collection context */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $collection;

    /** Optional item context */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $item;

    #[ORM\Column(type: 'boolean')]
    private bool $read = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $timestamp;

    public function __construct(
        string  $recipientId,
        string  $subject,
        string  $message,
        ?string $senderId   = null,
        ?string $collection = null,
        ?string $item       = null,
    ) {
        $this->id          = Uuid::v7()->toRfc4122();
        $this->recipientId = $recipientId;
        $this->subject     = $subject;
        $this->message     = $message;
        $this->senderId    = $senderId;
        $this->collection  = $collection;
        $this->item        = $item;
        $this->read        = false;
        $this->timestamp   = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getRecipientId(): string { return $this->recipientId; }
    public function getSubject(): string { return $this->subject; }
    public function getMessage(): string { return $this->message; }
    public function getSenderId(): ?string { return $this->senderId; }
    public function getCollection(): ?string { return $this->collection; }
    public function getItem(): ?string { return $this->item; }
    public function isRead(): bool { return $this->read; }
    public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }

    public function markAsRead(): static
    {
        $this->read = true;
        return $this;
    }

    public function isOwnedBy(string $userId): bool
    {
        return $this->recipientId === $userId;
    }
}
