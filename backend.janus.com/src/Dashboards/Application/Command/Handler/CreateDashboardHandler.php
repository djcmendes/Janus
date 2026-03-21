<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler;

use App\Dashboards\Application\Command\CreateDashboardCommand;
use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

final class CreateDashboardHandler
{
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    public function handle(CreateDashboardCommand $command): DashboardDto
    {
        $dashboard = new Dashboard(
            $command->name,
            $command->icon,
            $command->note,
            $command->userId,
        );

        $this->repository->save($dashboard);

        return DashboardDto::fromEntity($dashboard);
    }
}
