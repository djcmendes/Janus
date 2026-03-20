<?php

declare(strict_types=1);

namespace App\Flows\Application\Query\Handler;

use App\Flows\Application\DTO\FlowDto;
use App\Flows\Application\Query\GetFlowByIdQuery;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Domain\Repository\FlowRepositoryInterface;

final class GetFlowByIdHandler
{
    public function __construct(private readonly FlowRepositoryInterface $repository) {}

    public function handle(GetFlowByIdQuery $query): FlowDto
    {
        $flow = $this->repository->findById($query->id);

        if ($flow === null) {
            throw new FlowNotFoundException($query->id);
        }

        return FlowDto::fromEntity($flow);
    }
}
