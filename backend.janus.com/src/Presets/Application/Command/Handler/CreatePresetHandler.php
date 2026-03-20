<?php

declare(strict_types=1);

namespace App\Presets\Application\Command\Handler;

use App\Presets\Application\Command\CreatePresetCommand;
use App\Presets\Application\DTO\PresetDto;
use App\Presets\Domain\Entity\Preset;
use App\Presets\Domain\Repository\PresetRepositoryInterface;

final class CreatePresetHandler
{
    public function __construct(private readonly PresetRepositoryInterface $repository) {}

    public function handle(CreatePresetCommand $command): PresetDto
    {
        $preset = new Preset(
            $command->collection,
            $command->layout,
            $command->layoutOptions,
            $command->layoutQuery,
            $command->filter,
            $command->search,
            $command->bookmark,
            $command->userId,
        );

        $this->repository->save($preset);

        return PresetDto::fromEntity($preset);
    }
}
