<?php

declare(strict_types=1);

namespace App\Flows\Application\Query\Handler;

use App\Flows\Application\DTO\OperationDto;
use App\Flows\Application\Query\GetOperationsQuery;
use App\Flows\Domain\Repository\OperationRepositoryInterface;

final class GetOperationsHandler
{
    public function __construct(private readonly OperationRepositoryInterface $repository) {}

    /** @return array{data: OperationDto[], total: int} */
    public function handle(GetOperationsQuery $query): array
    {
        $operations = $this->repository->findAll($query->limit, $query->offset, $query->flowId);
        $total      = $this->repository->countAll($query->flowId);

        return [
            'data'  => array_map(OperationDto::fromEntity(...), $operations),
            'total' => $total,
        ];
    }
}
