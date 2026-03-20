<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\DeleteOperationCommand;
use App\Flows\Domain\Exception\OperationNotFoundException;
use App\Flows\Domain\Repository\OperationRepositoryInterface;

final class DeleteOperationHandler
{
    public function __construct(private readonly OperationRepositoryInterface $repository) {}

    public function handle(DeleteOperationCommand $command): void
    {
        $operation = $this->repository->findById($command->id);

        if ($operation === null) {
            throw new OperationNotFoundException($command->id);
        }

        $this->repository->delete($operation);
    }
}
