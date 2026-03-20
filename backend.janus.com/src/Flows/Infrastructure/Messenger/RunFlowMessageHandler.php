<?php

declare(strict_types=1);

namespace App\Flows\Infrastructure\Messenger;

use App\Flows\Domain\Message\RunFlowMessage;
use App\Flows\Domain\Repository\FlowRepositoryInterface;
use App\Flows\Domain\Repository\OperationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Consumes RunFlowMessage from the bus.
 *
 * Iterates operations in sortOrder, executing each by type.
 * This is a minimal scaffold — individual operation type handlers
 * would be wired in as the platform grows.
 */
#[AsMessageHandler]
final class RunFlowMessageHandler
{
    public function __construct(
        private readonly FlowRepositoryInterface      $flowRepository,
        private readonly OperationRepositoryInterface $operationRepository,
        private readonly LoggerInterface              $logger,
    ) {}

    public function __invoke(RunFlowMessage $message): void
    {
        $flow = $this->flowRepository->findById($message->flowId);

        if ($flow === null || !$flow->isActive()) {
            $this->logger->warning('Flow not found or inactive', ['flow_id' => $message->flowId]);
            return;
        }

        $operations = $this->operationRepository->findAll(1000, 0, $message->flowId);

        $this->logger->info('Running flow', [
            'flow_id'        => $flow->getId(),
            'flow_name'      => $flow->getName(),
            'operations'     => count($operations),
            'triggered_by'   => $message->triggeredBy,
        ]);

        $context = $message->payload;

        foreach ($operations as $operation) {
            $this->logger->info('Executing operation', [
                'operation_id'   => $operation->getId(),
                'operation_name' => $operation->getName(),
                'type'           => $operation->getType(),
            ]);

            // Scaffold: individual operation type execution is intentionally
            // deferred — extend here with a strategy/handler per type.
            $context['__last_operation'] = $operation->getId();
        }
    }
}
