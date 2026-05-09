<?php

/**
 * @file DashboardRepositoryInterface.php
 *
 * Domain contract for dashboard persistence operations.
 *
 * @package App\Dashboards\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Repository;

use App\Dashboards\Domain\Entity\Dashboard;

/**
 * Defines the persistence contract for the Dashboard aggregate.
 *
 * Implementations live in the Infrastructure layer; callers depend only on this interface.
 */
interface DashboardRepositoryInterface
{
    /**
     * Persists a Dashboard (insert or update).
     *
     * @param Dashboard $dashboard The dashboard to persist.
     *
     * @return void
     */
    public function save(Dashboard $dashboard): void;

    /**
     * Removes a Dashboard from persistence.
     *
     * @param Dashboard $dashboard The dashboard to remove.
     *
     * @return void
     */
    public function delete(Dashboard $dashboard): void;

    /**
     * Finds a Dashboard by its UUID, or returns null when not found.
     *
     * @param  string        $id UUID of the dashboard to find.
     * @return Dashboard|null    The domain entity, or null.
     */
    public function findById(string $id): ?Dashboard;

    /**
     * Returns a paginated slice of dashboards, optionally filtered by owner.
     *
     * @param  int         $limit   Maximum number of records to return.
     * @param  int         $offset  Zero-based record offset.
     * @param  string|null $userId  Owner UUID filter; null returns all.
     * @return Dashboard[]          Array of domain Dashboard entities.
     */
    public function findPaginated(int $limit, int $offset, ?string $userId = null): array;

    /**
     * Counts total dashboards, optionally filtered by owner.
     *
     * @param  string|null $userId Owner UUID filter; null counts all.
     * @return int                  Total number of matching dashboards.
     */
    public function countAll(?string $userId = null): int;
}
