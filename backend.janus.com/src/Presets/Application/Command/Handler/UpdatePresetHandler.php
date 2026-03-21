<?php

declare(strict_types=1);

namespace App\Presets\Application\Command\Handler;

use App\Presets\Application\Command\UpdatePresetCommand;
use App\Presets\Application\DTO\PresetDto;
use App\Presets\Domain\Exception\PresetForbiddenException;
use App\Presets\Domain\Exception\PresetNotFoundException;
use App\Presets\Domain\Repository\PresetRepositoryInterface;

final class UpdatePresetHandler
{
    public function __construct(private readonly PresetRepositoryInterface $repository) {}

    public function handle(UpdatePresetCommand $command): PresetDto
    {
        $preset = $this->repository->findById($command->id);

        if ($preset === null) {
            throw new PresetNotFoundException($command->id);
        }

        // Non-admin users can only modify their own presets
        if (!$command->isAdmin) {
            if ($preset->getUserId() === null || !$preset->isOwnedBy($command->requestingUserId)) {
                throw new PresetForbiddenException();
            }
        }

        if ($command->collection !== UpdatePresetCommand::UNCHANGED) {
            $preset->setCollection($command->collection);
        }
        if ($command->layout !== UpdatePresetCommand::UNCHANGED) {
            $preset->setLayout($command->layout);
        }
        if ($command->layoutOptions !== UpdatePresetCommand::UNCHANGED) {
            $preset->setLayoutOptions($command->layoutOptions);
        }
        if ($command->layoutQuery !== UpdatePresetCommand::UNCHANGED) {
            $preset->setLayoutQuery($command->layoutQuery);
        }
        if ($command->filter !== UpdatePresetCommand::UNCHANGED) {
            $preset->setFilter($command->filter);
        }
        if ($command->search !== UpdatePresetCommand::UNCHANGED) {
            $preset->setSearch($command->search);
        }
        if ($command->bookmark !== UpdatePresetCommand::UNCHANGED) {
            $preset->setBookmark($command->bookmark);
        }

        $this->repository->save($preset);

        return PresetDto::fromEntity($preset);
    }
}
