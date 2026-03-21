<?php

declare(strict_types=1);

namespace App\Panels\Application\Command\Handler;

use App\Panels\Application\Command\UpdatePanelCommand;
use App\Panels\Application\DTO\PanelDto;
use App\Panels\Domain\Exception\PanelNotFoundException;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

final class UpdatePanelHandler
{
    public function __construct(private readonly PanelRepositoryInterface $repository) {}

    public function handle(UpdatePanelCommand $command): PanelDto
    {
        $panel = $this->repository->findById($command->id);

        if ($panel === null) {
            throw new PanelNotFoundException($command->id);
        }

        if ($command->type !== UpdatePanelCommand::UNCHANGED) {
            $panel->setType($command->type);
        }
        if ($command->name !== UpdatePanelCommand::UNCHANGED) {
            $panel->setName($command->name);
        }
        if ($command->note !== UpdatePanelCommand::UNCHANGED) {
            $panel->setNote($command->note);
        }
        if ($command->options !== UpdatePanelCommand::UNCHANGED) {
            $panel->setOptions($command->options);
        }

        $xChanged = $command->positionX !== UpdatePanelCommand::UNCHANGED;
        $yChanged = $command->positionY !== UpdatePanelCommand::UNCHANGED;
        if ($xChanged || $yChanged) {
            $panel->setPosition(
                $xChanged ? (int) $command->positionX : $panel->getPositionX(),
                $yChanged ? (int) $command->positionY : $panel->getPositionY(),
            );
        }

        $wChanged = $command->width !== UpdatePanelCommand::UNCHANGED;
        $hChanged = $command->height !== UpdatePanelCommand::UNCHANGED;
        if ($wChanged || $hChanged) {
            $panel->setSize(
                $wChanged ? (int) $command->width : $panel->getWidth(),
                $hChanged ? (int) $command->height : $panel->getHeight(),
            );
        }

        $this->repository->save($panel);

        return PanelDto::fromEntity($panel);
    }
}
