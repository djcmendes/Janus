<?php

declare(strict_types=1);

namespace App\Panels\Application\Command\Handler;

use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Panels\Application\Command\CreatePanelCommand;
use App\Panels\Application\DTO\PanelDto;
use App\Panels\Domain\Entity\Panel;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

final class CreatePanelHandler
{
    public function __construct(
        private readonly PanelRepositoryInterface     $repository,
        private readonly DashboardRepositoryInterface $dashboardRepository,
    ) {}

    public function handle(CreatePanelCommand $command): PanelDto
    {
        if ($this->dashboardRepository->findById($command->dashboardId) === null) {
            throw new DashboardNotFoundException($command->dashboardId);
        }

        $panel = new Panel(
            $command->dashboardId,
            $command->type,
            $command->name,
            $command->note,
            $command->options,
            $command->positionX,
            $command->positionY,
            $command->width,
            $command->height,
        );

        $this->repository->save($panel);

        return PanelDto::fromEntity($panel);
    }
}
