<?php

/**
 * @file DeploymentDto.php
 *
 * Data Transfer Object representing a deployment run record in API responses.
 *
 * @package App\Deployments\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO;

use App\Deployments\Domain\Entity\Deployment;

/**
 * Immutable DTO carrying deployment run data for serialisation into API responses.
 */
final class DeploymentDto
{
    /**
     * @param string      $id          UUID string of the deployment run.
     * @param string      $providerId  UUID string of the triggering provider.
     * @param string      $status      Run lifecycle state (pending, running, success, failure).
     * @param string|null $log         Provider response body or error message.
     * @param string|null $triggeredBy UUID of the user who triggered the run.
     * @param string      $startedAt   ISO-8601 start timestamp.
     * @param string|null $completedAt ISO-8601 completion timestamp, or null if still running.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $providerId,
        public readonly string  $status,
        public readonly ?string $log,
        public readonly ?string $triggeredBy,
        public readonly string  $startedAt,
        public readonly ?string $completedAt,
    ) {}

    /**
     * Constructs a DeploymentDto from a domain Deployment entity.
     *
     * @param  Deployment    $d The domain entity to convert.
     * @return self              A DTO populated from the entity's current state.
     */
    public static function fromEntity(Deployment $d): self
    {
        return new self(
            id:          $d->getId(),
            providerId:  $d->getProviderId(),
            status:      $d->getStatus()->value,
            log:         $d->getLog(),
            triggeredBy: $d->getTriggeredBy(),
            startedAt:   $d->getStartedAt()->format(\DateTimeInterface::ATOM),
            completedAt: $d->getCompletedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
}
