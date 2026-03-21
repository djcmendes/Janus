<?php

declare(strict_types=1);

namespace App\Activity\Application\DTO;

use App\Activity\Domain\Entity\Activity;

final class ActivityDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $action,
        public readonly ?string $collection,
        public readonly ?string $item,
        public readonly ?string $userId,
        public readonly ?string $ip,
        public readonly ?string $userAgent,
        public readonly string  $timestamp,
    ) {}

    public static function fromEntity(Activity $a): self
    {
        return new self(
            id:         (string) $a->getId(),
            action:     $a->getAction(),
            collection: $a->getCollection(),
            item:       $a->getItem(),
            userId:     $a->getUserId(),
            ip:         $a->getIp(),
            userAgent:  $a->getUserAgent(),
            timestamp:  $a->getTimestamp()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'action'     => $this->action,
            'collection' => $this->collection,
            'item'       => $this->item,
            'user'       => $this->userId,
            'ip'         => $this->ip,
            'user_agent' => $this->userAgent,
            'timestamp'  => $this->timestamp,
        ];
    }
}
