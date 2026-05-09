<?php

/**
 * @file DeploymentRepositoryInterface.php
 *
 * Domain repository contract for Deployment run records.
 *
 * @package App\Deployments\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Repository;

use App\Deployments\Domain\Entity\Deployment;

/**
 * Persistence gateway for Deployment domain entities.
 *
 * Infrastructure implementations must not leak ORM concerns beyond this interface.
 */
interface DeploymentRepositoryInterface
{
    /**
     * Persists a Deployment run record.
     *
     * @param Deployment $deployment The run record to persist.
     * @param bool       $flush      Whether to flush immediately (default true).
     */
    public function save(Deployment $deployment, bool $flush = true): void;

    /**
     * Finds a Deployment run by UUID, or returns null when not found.
     *
     * @param  string          $id UUID of the run to retrieve.
     * @return Deployment|null     The matching domain entity, or null.
     */
    public function findById(string $id): ?Deployment;

    /**
     * Returns a paginated slice of deployment runs, optionally filtered by provider.
     *
     * @param  int         $limit      Maximum number of records.
     * @param  int         $offset     Zero-based record offset.
     * @param  string|null $providerId Filter by provider UUID; null returns all runs.
     * @return Deployment[]             Array of matching domain entities.
     */
    public function findPaginated(int $limit, int $offset, ?string $providerId = null): array;

    /**
     * Counts total deployment runs, optionally filtered by provider.
     *
     * @param  string|null $providerId Filter by provider UUID; null counts all runs.
     * @return int                      Total number of matching runs.
     */
    public function countAll(?string $providerId = null): int;
}
