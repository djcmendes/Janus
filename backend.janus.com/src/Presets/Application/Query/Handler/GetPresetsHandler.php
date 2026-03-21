<?php

declare(strict_types=1);

namespace App\Presets\Application\Query\Handler;

use App\Presets\Application\DTO\PresetDto;
use App\Presets\Application\Query\GetPresetsQuery;
use App\Presets\Domain\Repository\PresetRepositoryInterface;

final class GetPresetsHandler
{
    public function __construct(private readonly PresetRepositoryInterface $repository) {}

    /** @return array{data: PresetDto[], total: int} */
    public function handle(GetPresetsQuery $query): array
    {
        $presets = $this->repository->findPaginated($query->limit, $query->offset, $query->collection, $query->userId);
        $total   = $this->repository->countAll($query->collection, $query->userId);

        return [
            'data'  => array_map(PresetDto::fromEntity(...), $presets),
            'total' => $total,
        ];
    }
}
