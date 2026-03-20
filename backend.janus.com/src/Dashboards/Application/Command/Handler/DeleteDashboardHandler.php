<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler;

use App\Dashboards\Application\Command\DeleteDashboardCommand;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

final class DeleteDashboardHandler
{
    public function __construct(
        private readonly DashboardRepositoryInterface $repository,
        private readonly PanelRepositoryInterface     $panelRepository,
    ) {}

    public function handle(DeleteDashboardCommand $command): void
    {
        $dashboard = $this->repository->findById($command->id);

        if ($dashboard === null) {
            throw new DashboardNotFoundException($command->id);
        }

        // Cascade: remove all panels belonging to this dashboard first
        $this->panelRepository->deleteByDashboard($command->id);

        $this->repository->delete($dashboard);
    }
}
