<?php

declare(strict_types=1);

namespace App\Flows\Application\Query\Handler;

use App\Flows\Application\DTO\OperationDto;
use App\Flows\Application\Query\GetOperationByIdQuery;
use App\Flows\Domain\Exception\OperationNotFoundException;
use App\Flows\Domain\Repository\OperationRepositoryInterface;

final class GetOperationByIdHandler
{
    public function __construct(private readonly OperationRepositoryInterface $repository) {}

    public function handle(GetOperationByIdQuery $query): OperationDto
    {
        $operation = $this->repository->findById($query->id);

        if ($operation === null) {
            throw new OperationNotFoundException($query->id);
        }

        return OperationDto::fromEntity($operation);
    }
}
