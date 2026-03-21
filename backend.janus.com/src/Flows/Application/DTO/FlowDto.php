<?php

declare(strict_types=1);

namespace App\Flows\Application\DTO;

use App\Flows\Domain\Entity\Flow;

final class FlowDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $status,
        public readonly string  $trigger,
        public readonly ?array  $triggerOptions,
        public readonly ?string $userId,
        public readonly ?string $description,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    public static function fromEntity(Flow $flow): self
    {
        return new self(
            id:             $flow->getId(),
            name:           $flow->getName(),
            status:         $flow->getStatus()->value,
            trigger:        $flow->getTrigger()->value,
            triggerOptions: $flow->getTriggerOptions(),
            userId:         $flow->getUserId(),
            description:    $flow->getDescription(),
            createdAt:      $flow->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:      $flow->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
