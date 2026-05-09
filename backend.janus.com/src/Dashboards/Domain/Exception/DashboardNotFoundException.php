<?php

/**
 * @file DashboardNotFoundException.php
 *
 * Domain exception thrown when a requested dashboard does not exist.
 *
 * @package App\Dashboards\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Exception;

/**
 * Thrown by repository implementations and query handlers when a dashboard UUID
 * yields no result in persistence.
 */
final class DashboardNotFoundException extends \RuntimeException
{
    /**
     * @param string $id UUID of the dashboard that was not found.
     */
    public function __construct(string $id)
    {
        parent::__construct("Dashboard '{$id}' not found.");
    }
}
