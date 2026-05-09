<?php

/**
 * @file CreateDashboardHandler.php
 *
 * Handles dashboard creation commands.
 *
 * @package App\Dashboards\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler;

use App\Dashboards\Application\Command\CreateDashboardCommand;
use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

/**
 * Creates a new Dashboard and persists it, returning a populated DTO.
 */
final class CreateDashboardHandler
{
    /**
     * @param DashboardRepositoryInterface $repository Persistence layer for dashboards.
     */
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    /**
     * Executes the create-dashboard command.
     *
     * @param  CreateDashboardCommand $command Data for the new dashboard.
     * @return DashboardDto                    DTO reflecting the newly persisted record.
     */
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
