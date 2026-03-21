<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\TriggerFlowCommand;
use App\Flows\Domain\Exception\FlowInactiveException;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Domain\Repository\FlowRepositoryInterface;
use App\Flows\Domain\Service\FlowRunnerService;

final class TriggerFlowHandler
{
    public function __construct(
        private readonly FlowRepositoryInterface $repository,
        private readonly FlowRunnerService       $runner,
    ) {}

    public function handle(TriggerFlowCommand $command): void
    {
        $flow = $this->repository->findById($command->flowId);

        if ($flow === null) {
            throw new FlowNotFoundException($command->flowId);
        }

        if (!$flow->isActive()) {
            throw new FlowInactiveException($command->flowId);
        }

        $this->runner->dispatch($flow, $command->payload, $command->triggeredBy);
    }
}
