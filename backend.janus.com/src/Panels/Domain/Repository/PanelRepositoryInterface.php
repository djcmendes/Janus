<?php

declare(strict_types=1);

namespace App\Panels\Domain\Repository;

use App\Panels\Domain\Entity\Panel;

interface PanelRepositoryInterface
{
    public function save(Panel $panel): void;

    public function delete(Panel $panel): void;

    public function findById(string $id): ?Panel;

    /** @return Panel[] */
    public function findAll(int $limit, int $offset, ?string $dashboardId = null): array;

    public function countAll(?string $dashboardId = null): int;

    public function deleteByDashboard(string $dashboardId): void;
}
