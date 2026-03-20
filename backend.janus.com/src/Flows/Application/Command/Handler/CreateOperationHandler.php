<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\CreateOperationCommand;
use App\Flows\Application\DTO\OperationDto;
use App\Flows\Domain\Entity\Operation;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Domain\Repository\FlowRepositoryInterface;
use App\Flows\Domain\Repository\OperationRepositoryInterface;

final class CreateOperationHandler
{
    public function __construct(
        private readonly OperationRepositoryInterface $repository,
        private readonly FlowRepositoryInterface      $flowRepository,
    ) {}

    public function handle(CreateOperationCommand $command): OperationDto
    {
        if ($this->flowRepository->findById($command->flowId) === null) {
            throw new FlowNotFoundException($command->flowId);
        }

        $operation = new Operation(
            $command->flowId,
            $command->name,
            $command->type,
            $command->options,
            $command->resolve,
            $command->nextSuccess,
            $command->nextFailure,
            $command->sortOrder,
        );

        $this->repository->save($operation);

        return OperationDto::fromEntity($operation);
    }
}
