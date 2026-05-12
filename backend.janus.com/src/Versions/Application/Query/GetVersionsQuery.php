<?php

/**
 * @file GetVersionsQuery.php
 *
 * Read-side payload for fetching a paginated list of Version records.
 *
 * @package App\Versions\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query;

/**
 * Carries pagination and optional filter parameters for the versions list endpoint.
 */
final class GetVersionsQuery
{
    /**
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip for pagination.
     * @param string|null $collection Filter to versions for a specific collection name, or null for all.
     * @param string|null $item       Filter to versions for a specific item identifier, or null for all.
     */
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection,
        public readonly ?string $item,
    ) {}
}
