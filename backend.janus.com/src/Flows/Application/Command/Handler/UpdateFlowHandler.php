<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\UpdateFlowCommand;
use App\Flows\Application\DTO\FlowDto;
use App\Flows\Domain\Enum\FlowStatus;
use App\Flows\Domain\Enum\TriggerType;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Domain\Repository\FlowRepositoryInterface;

final class UpdateFlowHandler
{
    public function __construct(private readonly FlowRepositoryInterface $repository) {}

    public function handle(UpdateFlowCommand $command): FlowDto
    {
        $flow = $this->repository->findById($command->id);

        if ($flow === null) {
            throw new FlowNotFoundException($command->id);
        }

        if ($command->name !== UpdateFlowCommand::UNCHANGED) {
            $flow->setName($command->name);
        }
        if ($command->status !== UpdateFlowCommand::UNCHANGED) {
            $flow->setStatus(FlowStatus::from($command->status));
        }
        if ($command->trigger !== UpdateFlowCommand::UNCHANGED) {
            $flow->setTrigger(TriggerType::from($command->trigger));
        }
        if ($command->triggerOptions !== UpdateFlowCommand::UNCHANGED) {
            $flow->setTriggerOptions($command->triggerOptions);
        }
        if ($command->description !== UpdateFlowCommand::UNCHANGED) {
            $flow->setDescription($command->description);
        }

        $this->repository->save($flow);

        return FlowDto::fromEntity($flow);
    }
}
