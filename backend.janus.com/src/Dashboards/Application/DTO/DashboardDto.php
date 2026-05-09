<?php

/**
 * @file DashboardDto.php
 *
 * Data Transfer Object representing a dashboard in API responses.
 *
 * @package App\Dashboards\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\DTO;

use App\Dashboards\Domain\Entity\Dashboard;

/**
 * Immutable DTO carrying dashboard data for serialisation into API responses.
 */
final class DashboardDto
{
    /**
     * @param string      $id        UUID string of the dashboard.
     * @param string      $name      Human-readable display name.
     * @param string|null $icon      Optional icon identifier.
     * @param string|null $note      Optional descriptive note.
     * @param string|null $userId    Owner user UUID, or null for shared/global dashboards.
     * @param string      $createdAt ISO-8601 creation timestamp.
     * @param string      $updatedAt ISO-8601 last-modification timestamp.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $icon,
        public readonly ?string $note,
        public readonly ?string $userId,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    /**
     * Constructs a DashboardDto from a domain Dashboard entity.
     *
     * @param  Dashboard    $dashboard The domain entity to convert.
     * @return self                    A DTO populated from the entity's current state.
     */
    public static function fromEntity(Dashboard $dashboard): self
    {
        return new self(
            id:        $dashboard->getId(),
            name:      $dashboard->getName(),
            icon:      $dashboard->getIcon(),
            note:      $dashboard->getNote(),
            userId:    $dashboard->getUserId(),
            createdAt: $dashboard->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $dashboard->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
