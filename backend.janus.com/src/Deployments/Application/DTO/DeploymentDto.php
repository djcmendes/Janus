<?php

declare(strict_types=1);

namespace App\Deployments\Application\DTO;

use App\Deployments\Domain\Entity\Deployment;

final class DeploymentDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $providerId,
        public readonly string  $status,
        public readonly ?string $log,
        public readonly ?string $triggeredBy,
        public readonly string  $startedAt,
        public readonly ?string $completedAt,
    ) {}

    public static function fromEntity(Deployment $d): self
    {
        return new self(
            id:          (string) $d->getId(),
            providerId:  $d->getProviderId(),
            status:      $d->getStatus()->value,
            log:         $d->getLog(),
            triggeredBy: $d->getTriggeredBy(),
            startedAt:   $d->getStartedAt()->format(\DateTimeInterface::ATOM),
            completedAt: $d->getCompletedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
}
