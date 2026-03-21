<?php

declare(strict_types=1);

namespace App\Panels\Application\Query\Handler;

use App\Panels\Application\DTO\PanelDto;
use App\Panels\Application\Query\GetPanelsQuery;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

final class GetPanelsHandler
{
    public function __construct(private readonly PanelRepositoryInterface $repository) {}

    /** @return array{data: PanelDto[], total: int} */
    public function handle(GetPanelsQuery $query): array
    {
        $panels = $this->repository->findPaginated($query->limit, $query->offset, $query->dashboardId);
        $total  = $this->repository->countAll($query->dashboardId);

        return [
            'data'  => array_map(PanelDto::fromEntity(...), $panels),
            'total' => $total,
        ];
    }
}
