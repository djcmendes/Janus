<?php

/**
 * @file DeploymentProviderRepositoryInterface.php
 *
 * Domain repository contract for DeploymentProvider records.
 *
 * @package App\Deployments\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Repository;

use App\Deployments\Domain\Entity\DeploymentProvider;

/**
 * Persistence gateway for DeploymentProvider domain entities.
 *
 * Infrastructure implementations must not leak ORM concerns beyond this interface.
 */
interface DeploymentProviderRepositoryInterface
{
    /**
     * Persists a DeploymentProvider.
     *
     * @param DeploymentProvider $provider The provider to persist.
     * @param bool               $flush    Whether to flush immediately (default true).
     */
    public function save(DeploymentProvider $provider, bool $flush = true): void;

    /**
     * Removes a DeploymentProvider from persistence.
     *
     * @param DeploymentProvider $provider The provider to remove.
     */
    public function delete(DeploymentProvider $provider): void;

    /**
     * Finds a DeploymentProvider by UUID, or returns null when not found.
     *
     * @param  string                  $id UUID of the provider to retrieve.
     * @return DeploymentProvider|null     The matching domain entity, or null.
     */
    public function findById(string $id): ?DeploymentProvider;

    /**
     * Returns a paginated slice of deployment providers.
     *
     * @param  int                  $limit  Maximum number of records.
     * @param  int                  $offset Zero-based record offset.
     * @return DeploymentProvider[]         Array of domain entities.
     */
    public function findPaginated(int $limit, int $offset): array;

    /**
     * Counts the total number of deployment providers.
     *
     * @return int Total count.
     */
    public function countAll(): int;
}
