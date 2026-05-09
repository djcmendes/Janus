<?php

/**
 * @file GetDashboardByIdQuery.php
 *
 * Query payload for retrieving a single dashboard by UUID.
 *
 * @package App\Dashboards\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query;

/**
 * Carries the UUID of the dashboard to retrieve.
 */
final class GetDashboardByIdQuery
{
    /**
     * @param string $id UUID of the dashboard to retrieve.
     */
    public function __construct(public readonly string $id) {}
}
