<?php

declare(strict_types=1);

namespace App\Presets\Application\Command\Handler;

use App\Presets\Application\Command\DeletePresetCommand;
use App\Presets\Domain\Exception\PresetForbiddenException;
use App\Presets\Domain\Exception\PresetNotFoundException;
use App\Presets\Domain\Repository\PresetRepositoryInterface;

final class DeletePresetHandler
{
    public function __construct(private readonly PresetRepositoryInterface $repository) {}

    public function handle(DeletePresetCommand $command): void
    {
        $preset = $this->repository->findById($command->id);

        if ($preset === null) {
            throw new PresetNotFoundException($command->id);
        }

        if (!$command->isAdmin) {
            if ($preset->getUserId() === null || !$preset->isOwnedBy($command->requestingUserId)) {
                throw new PresetForbiddenException();
            }
        }

        $this->repository->delete($preset);
    }
}
