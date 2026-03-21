<?php

declare(strict_types=1);

namespace App\Flows\Application\DTO;

use App\Flows\Domain\Entity\Operation;

final class OperationDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $flowId,
        public readonly string  $name,
        public readonly string  $type,
        public readonly ?array  $options,
        public readonly ?string $resolve,
        public readonly ?string $nextSuccess,
        public readonly ?string $nextFailure,
        public readonly int     $sortOrder,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    public static function fromEntity(Operation $operation): self
    {
        return new self(
            id:          $operation->getId(),
            flowId:      $operation->getFlowId(),
            name:        $operation->getName(),
            type:        $operation->getType(),
            options:     $operation->getOptions(),
            resolve:     $operation->getResolve(),
            nextSuccess: $operation->getNextSuccess(),
            nextFailure: $operation->getNextFailure(),
            sortOrder:   $operation->getSortOrder(),
            createdAt:   $operation->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:   $operation->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
