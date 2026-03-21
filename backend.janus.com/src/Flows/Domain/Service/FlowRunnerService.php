<?php

declare(strict_types=1);

namespace App\Flows\Domain\Service;

use App\Flows\Domain\Entity\Flow;
use App\Flows\Domain\Message\RunFlowMessage;
use App\Flows\Domain\Repository\OperationRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Dispatches a flow for async execution via Symfony Messenger.
 *
 * The actual operation-by-operation execution logic belongs in a
 * message handler (consumer). This service is the entry point that
 * enqueues work and returns immediately.
 */
final class FlowRunnerService
{
    public function __construct(
        private readonly MessageBusInterface         $bus,
        private readonly OperationRepositoryInterface $operationRepository,
    ) {}

    public function dispatch(Flow $flow, array $payload = [], ?string $triggeredBy = null): void
    {
        $this->bus->dispatch(new RunFlowMessage($flow->getId(), $payload, $triggeredBy));
    }
}
