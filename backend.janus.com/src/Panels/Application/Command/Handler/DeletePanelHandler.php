<?php

declare(strict_types=1);

namespace App\Panels\Application\Command\Handler;

use App\Panels\Application\Command\DeletePanelCommand;
use App\Panels\Domain\Exception\PanelNotFoundException;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

final class DeletePanelHandler
{
    public function __construct(private readonly PanelRepositoryInterface $repository) {}

    public function handle(DeletePanelCommand $command): void
    {
        $panel = $this->repository->findById($command->id);

        if ($panel === null) {
            throw new PanelNotFoundException($command->id);
        }

        $this->repository->delete($panel);
    }
}
