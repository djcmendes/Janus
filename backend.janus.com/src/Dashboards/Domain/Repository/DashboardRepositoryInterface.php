<?php

declare(strict_types=1);

namespace App\Dashboards\Domain\Repository;

use App\Dashboards\Domain\Entity\Dashboard;

interface DashboardRepositoryInterface
{
    public function save(Dashboard $dashboard): void;

    public function delete(Dashboard $dashboard): void;

    public function findById(string $id): ?Dashboard;

    /** @return Dashboard[] */
    public function findAll(int $limit, int $offset, ?string $userId = null): array;

    public function countAll(?string $userId = null): int;
}
