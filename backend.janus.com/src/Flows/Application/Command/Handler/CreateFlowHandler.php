<?php

declare(strict_types=1);

namespace App\Flows\Application\Command\Handler;

use App\Flows\Application\Command\CreateFlowCommand;
use App\Flows\Application\DTO\FlowDto;
use App\Flows\Domain\Entity\Flow;
use App\Flows\Domain\Enum\FlowStatus;
use App\Flows\Domain\Enum\TriggerType;
use App\Flows\Domain\Repository\FlowRepositoryInterface;

final class CreateFlowHandler
{
    public function __construct(private readonly FlowRepositoryInterface $repository) {}

    public function handle(CreateFlowCommand $command): FlowDto
    {
        $flow = new Flow(
            $command->name,
            FlowStatus::from($command->status),
            TriggerType::from($command->trigger),
            $command->triggerOptions,
            $command->userId,
            $command->description,
        );

        $this->repository->save($flow);

        return FlowDto::fromEntity($flow);
    }
}
