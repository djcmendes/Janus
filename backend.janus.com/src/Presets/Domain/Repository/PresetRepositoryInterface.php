<?php

declare(strict_types=1);

namespace App\Presets\Domain\Repository;

use App\Presets\Domain\Entity\Preset;

interface PresetRepositoryInterface
{
    public function save(Preset $preset): void;

    public function delete(Preset $preset): void;

    public function findById(string $id): ?Preset;

    /** @return Preset[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $userId     = null,
    ): array;

    public function countAll(
        ?string $collection = null,
        ?string $userId     = null,
    ): int;
}
