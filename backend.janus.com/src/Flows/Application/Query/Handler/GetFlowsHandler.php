<?php

declare(strict_types=1);

namespace App\Flows\Application\Query\Handler;

use App\Flows\Application\DTO\FlowDto;
use App\Flows\Application\Query\GetFlowsQuery;
use App\Flows\Domain\Repository\FlowRepositoryInterface;

final class GetFlowsHandler
{
    public function __construct(private readonly FlowRepositoryInterface $repository) {}

    /** @return array{data: FlowDto[], total: int} */
    public function handle(GetFlowsQuery $query): array
    {
        $flows = $this->repository->findAll($query->limit, $query->offset, $query->status);
        $total = $this->repository->countAll($query->status);

        return [
            'data'  => array_map(FlowDto::fromEntity(...), $flows),
            'total' => $total,
        ];
    }
}
