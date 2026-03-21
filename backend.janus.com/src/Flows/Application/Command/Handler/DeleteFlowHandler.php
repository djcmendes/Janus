<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\DeleteFlowCommand;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Domain\Repository\FlowRepositoryInterface;
use App\Flows\Domain\Repository\OperationRepositoryInterface;

final class DeleteFlowHandler
{
    public function __construct(
        private readonly FlowRepositoryInterface      $repository,
        private readonly OperationRepositoryInterface $operationRepository,
    ) {}

    public function handle(DeleteFlowCommand $command): void
    {
        $flow = $this->repository->findById($command->id);

        if ($flow === null) {
            throw new FlowNotFoundException($command->id);
        }

        // Cascade: remove all operations belonging to this flow
        $this->operationRepository->deleteByFlow($command->id);

        $this->repository->delete($flow);
    }
}
