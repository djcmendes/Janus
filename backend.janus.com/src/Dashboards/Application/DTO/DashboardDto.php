<?php

declare(strict_types=1);

namespace App\Dashboards\Application\DTO;

use App\Dashboards\Domain\Entity\Dashboard;

final class DashboardDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $icon,
        public readonly ?string $note,
        public readonly ?string $userId,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

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
