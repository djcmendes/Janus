<?php

/**
 * @file ExtensionRepositoryInterface.php
 *
 * Domain repository contract for Extension records.
 *
 * @package App\Extensions\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Domain\Repository;

use App\Extensions\Domain\Entity\Extension;

/**
 * Persistence gateway for Extension domain entities.
 *
 * Infrastructure implementations must not leak ORM concerns beyond this interface.
 */
interface ExtensionRepositoryInterface
{
    /**
     * Persists an Extension record.
     *
     * @param Extension $extension The extension to persist.
     */
    public function save(Extension $extension): void;

    /**
     * Removes an Extension record from persistence.
     *
     * @param Extension $extension The extension to delete.
     */
    public function delete(Extension $extension): void;

    /**
     * Finds an Extension by UUID, or returns null when not found.
     *
     * @param  string         $id UUID of the extension to retrieve.
     * @return Extension|null     The matching domain entity, or null.
     */
    public function findById(string $id): ?Extension;

    /**
     * Returns a paginated slice of extensions, optionally filtered by type and enabled state.
     *
     * @param  int         $limit   Maximum number of records.
     * @param  int         $offset  Zero-based record offset.
     * @param  string|null $type    Filter by ExtensionType value string; null returns all types.
     * @param  bool|null   $enabled Filter by enabled state; null returns all.
     * @return Extension[]           Array of matching domain entities.
     */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $type    = null,
        ?bool   $enabled = null,
    ): array;

    /**
     * Counts total extensions, optionally filtered by type and enabled state.
     *
     * @param  string|null $type    Filter by ExtensionType value string; null counts all types.
     * @param  bool|null   $enabled Filter by enabled state; null counts all.
     * @return int                   Total number of matching records.
     */
    public function countAll(
        ?string $type    = null,
        ?bool   $enabled = null,
    ): int;
}
