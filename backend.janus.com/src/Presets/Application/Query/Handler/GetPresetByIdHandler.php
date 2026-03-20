<?php

declare(strict_types=1);

namespace App\Presets\Application\Query\Handler;

use App\Presets\Application\DTO\PresetDto;
use App\Presets\Application\Query\GetPresetByIdQuery;
use App\Presets\Domain\Exception\PresetNotFoundException;
use App\Presets\Domain\Repository\PresetRepositoryInterface;

final class GetPresetByIdHandler
{
    public function __construct(private readonly PresetRepositoryInterface $repository) {}

    public function handle(GetPresetByIdQuery $query): PresetDto
    {
        $preset = $this->repository->findById($query->id);

        if ($preset === null) {
            throw new PresetNotFoundException($query->id);
        }

        return PresetDto::fromEntity($preset);
    }
}
