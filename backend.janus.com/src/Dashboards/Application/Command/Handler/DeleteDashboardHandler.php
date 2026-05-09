<?php

/**
 * @file DeleteDashboardHandler.php
 *
 * Handles dashboard deletion commands, including cascade-deletion of panels.
 *
 * @package App\Dashboards\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler;

use App\Dashboards\Application\Command\DeleteDashboardCommand;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

/**
 * Deletes a Dashboard and cascade-removes all of its Panels.
 */
final class DeleteDashboardHandler
{
    /**
     * @param DashboardRepositoryInterface $repository     Persistence layer for dashboards.
     * @param PanelRepositoryInterface     $panelRepository Persistence layer for panels (cascade).
     */
    public function __construct(
        private readonly DashboardRepositoryInterface $repository,
        private readonly PanelRepositoryInterface     $panelRepository,
    ) {}

    /**
     * Executes the delete-dashboard command.
     *
     * All panels belonging to the dashboard are removed before the dashboard itself.
     *
     * @param  DeleteDashboardCommand  $command UUID of the dashboard to remove.
     * @return void
     *
     * @throws DashboardNotFoundException When no dashboard exists with the given UUID.
     */
    public function handle(DeleteDashboardCommand $command): void
    {
        $dashboard = $this->repository->findById($command->id);

        if ($dashboard === null) {
            throw new DashboardNotFoundException($command->id);
        }

        $this->panelRepository->deleteByDashboard($command->id);

        $this->repository->delete($dashboard);
    }
}
