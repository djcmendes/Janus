<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\UpdateOperationCommand;
use App\Flows\Application\DTO\OperationDto;
use App\Flows\Domain\Exception\OperationNotFoundException;
use App\Flows\Domain\Repository\OperationRepositoryInterface;

final class UpdateOperationHandler
{
    public function __construct(private readonly OperationRepositoryInterface $repository) {}

    public function handle(UpdateOperationCommand $command): OperationDto
    {
        $operation = $this->repository->findById($command->id);

        if ($operation === null) {
            throw new OperationNotFoundException($command->id);
        }

        if ($command->name !== UpdateOperationCommand::UNCHANGED) {
            $operation->setName($command->name);
        }
        if ($command->type !== UpdateOperationCommand::UNCHANGED) {
            $operation->setType($command->type);
        }
        if ($command->options !== UpdateOperationCommand::UNCHANGED) {
            $operation->setOptions($command->options);
        }
        if ($command->resolve !== UpdateOperationCommand::UNCHANGED) {
            $operation->setResolve($command->resolve);
        }
        if ($command->nextSuccess !== UpdateOperationCommand::UNCHANGED) {
            $operation->setNextSuccess($command->nextSuccess);
        }
        if ($command->nextFailure !== UpdateOperationCommand::UNCHANGED) {
            $operation->setNextFailure($command->nextFailure);
        }
        if ($command->sortOrder !== UpdateOperationCommand::UNCHANGED) {
            $operation->setSortOrder((int) $command->sortOrder);
        }

        $this->repository->save($operation);

        return OperationDto::fromEntity($operation);
    }
}
