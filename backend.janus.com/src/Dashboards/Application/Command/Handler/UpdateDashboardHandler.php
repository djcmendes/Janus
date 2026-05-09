<?php

/**
 * @file UpdateDashboardHandler.php
 *
 * Handles partial dashboard update commands.
 *
 * @package App\Dashboards\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler;

use App\Dashboards\Application\Command\UpdateDashboardCommand;
use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

/**
 * Applies a partial update to an existing Dashboard and persists the result.
 */
final class UpdateDashboardHandler
{
    /**
     * @param DashboardRepositoryInterface $repository Persistence layer for dashboards.
     */
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    /**
     * Executes the update-dashboard command.
     *
     * Only fields not set to UpdateDashboardCommand::UNCHANGED are applied.
     *
     * @param  UpdateDashboardCommand  $command Partial update payload.
     * @return DashboardDto                     DTO reflecting the updated record.
     *
     * @throws DashboardNotFoundException When no dashboard exists with the given UUID.
     */
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
