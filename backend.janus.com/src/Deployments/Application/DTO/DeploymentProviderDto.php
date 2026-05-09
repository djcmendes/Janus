<?php

/**
 * @file DeploymentProviderDto.php
 *
 * Data Transfer Object representing a deployment provider in API responses.
 *
 * @package App\Deployments\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO;

use App\Deployments\Domain\Entity\DeploymentProvider;

/**
 * Immutable DTO carrying deployment provider data for serialisation into API responses.
 */
final class DeploymentProviderDto
{
    /**
     * @param string                    $id        UUID string of the provider.
     * @param string                    $name      Human-readable provider name.
     * @param string                    $type      Integration type (webhook, netlify, vercel, custom).
     * @param string                    $url       Webhook or build-hook URL.
     * @param array<string, mixed>|null $options   Extra options, or null.
     * @param bool                      $isActive  Whether the provider is active.
     * @param string                    $createdAt ISO-8601 creation timestamp.
     * @param string|null               $updatedAt ISO-8601 last-modification timestamp, or null.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $url,
        public readonly ?array  $options,
        public readonly bool    $isActive,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /**
     * Constructs a DeploymentProviderDto from a domain DeploymentProvider entity.
     *
     * @param  DeploymentProvider $p The domain entity to convert.
     * @return self                   A DTO populated from the entity's current state.
     */
    public static function fromEntity(DeploymentProvider $p): self
    {
        return new self(
            id:        $p->getId(),
            name:      $p->getName(),
            type:      $p->getType()->value,
            url:       $p->getUrl(),
            options:   $p->getOptions(),
            isActive:  $p->isActive(),
            createdAt: $p->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $p->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
}
