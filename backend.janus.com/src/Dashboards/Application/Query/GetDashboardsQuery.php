<?php

/**
 * @file GetDashboardsQuery.php
 *
 * Query payload for retrieving a paginated list of dashboards.
 *
 * @package App\Dashboards\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query;

/**
 * Carries pagination parameters and an optional owner-filter for listing dashboards.
 */
final class GetDashboardsQuery
{
    /**
     * @param int         $limit  Maximum number of records to return.
     * @param int         $offset Zero-based record offset for pagination.
     * @param string|null $userId Owner UUID filter; null returns all dashboards.
     */
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $userId = null,
    ) {}
}
