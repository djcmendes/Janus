<?php

declare(strict_types=1);

namespace App\Shares\Application\DTO;

use App\Shares\Domain\Entity\Share;

final class ShareDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $token,
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $userId,
        public readonly ?string $name,
        public readonly bool    $hasPassword,
        public readonly ?string $expiresAt,
        public readonly ?int    $maxUses,
        public readonly int     $timesUsed,
        public readonly string  $createdAt,
    ) {}

    public static function fromEntity(Share $share): self
    {
        return new self(
            id:          $share->getId(),
            token:       $share->getToken(),
            collection:  $share->getCollection(),
            item:        $share->getItem(),
            userId:      $share->getUserId(),
            name:        $share->getName(),
            hasPassword: $share->getPassword() !== null,
            expiresAt:   $share->getExpiresAt()?->format(\DateTimeInterface::ATOM),
            maxUses:     $share->getMaxUses(),
            timesUsed:   $share->getTimesUsed(),
            createdAt:   $share->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
