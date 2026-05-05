<?php

/**
 * @file GetActivityQuery.php
 *
 * CQRS query payload for retrieving a paginated list of Activity records.
 *
 * @package App\Activity\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query;

/**
 * Query that carries pagination and filter parameters for the Activity list endpoint.
 */
final class GetActivityQuery
{
    /**
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to activities on a specific collection, or null for all.
     * @param string|null $action     Filter to a specific action type, or null for all.
     * @param string|null $userId     Filter to activities performed by a specific user UUID, or null for all.
     */
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection = null,
        public readonly ?string $action     = null,
        public readonly ?string $userId     = null,
    ) {}
}
