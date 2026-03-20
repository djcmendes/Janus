<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler;

use App\Dashboards\Application\Command\UpdateDashboardCommand;
use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

final class UpdateDashboardHandler
{
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    public function handle(UpdateDashboardCommand $command): DashboardDto
    {
        $dashboard = $this->repository->findById($command->id);

        if ($dashboard === null) {
            throw new DashboardNotFoundException($command->id);
        }

        if ($command->name !== UpdateDashboardCommand::UNCHANGED) {
            $dashboard->setName($command->name);
        }
        if ($command->icon !== UpdateDashboardCommand::UNCHANGED) {
            $dashboard->setIcon($command->icon);
        }
        if ($command->note !== UpdateDashboardCommand::UNCHANGED) {
            $dashboard->setNote($command->note);
        }

        $this->repository->save($dashboard);

        return DashboardDto::fromEntity($dashboard);
    }
}
