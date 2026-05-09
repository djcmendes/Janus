<?php

/**
 * @file DeleteDashboardCommand.php
 *
 * Command payload for deleting an existing dashboard.
 *
 * @package App\Dashboards\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command;

/**
 * Carries the UUID of the dashboard to delete.
 */
final class DeleteDashboardCommand
{
    /**
     * @param string $id UUID of the dashboard to delete.
     */
    public function __construct(public readonly string $id) {}
}
