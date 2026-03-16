<?php

declare(strict_types=1);

namespace App\Activity\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'activity')]
class Activity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 50)]
    private string $action; // create, update, delete, login, etc.

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $collection = null;

    #[ORM\Column(nullable: true)]
    private ?string $item = null;

    #[ORM\Column(nullable: true)]
    private ?string $userId = null;

    #[ORM\Column(nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column]
    private \DateTimeImmutable $timestamp;

    public function __construct(string $action, ?string $collection = null, ?string $item = null)
    {
        $this->id         = Uuid::v7();
        $this->action     = $action;
        $this->collection = $collection;
        $this->item       = $item;
        $this->timestamp  = new \DateTimeImmutable();
    }

    public function getId(): Uuid { return $this->id; }
    public function getAction(): string { return $this->action; }
    public function getCollection(): ?string { return $this->collection; }
    public function getItem(): ?string { return $this->item; }
    public function getUserId(): ?string { return $this->userId; }
    public function setUserId(?string $v): static { $this->userId = $v; return $this; }
    public function getIp(): ?string { return $this->ip; }
    public function setIp(?string $v): static { $this->ip = $v; return $this; }
    public function getUserAgent(): ?string { return $this->userAgent; }
    public function setUserAgent(?string $v): static { $this->userAgent = $v; return $this; }
    public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }

    public function toArray(): array
    {
        return [
            'id'         => (string) $this->id,
            'action'     => $this->action,
            'collection' => $this->collection,
            'item'       => $this->item,
            'user'       => $this->userId,
            'ip'         => $this->ip,
            'user_agent' => $this->userAgent,
            'timestamp'  => $this->timestamp->format(\DateTimeInterface::ATOM),
        ];
    }
}
