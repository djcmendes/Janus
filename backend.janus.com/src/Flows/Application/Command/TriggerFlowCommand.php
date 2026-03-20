<?php

declare(strict_types=1);

namespace App\Flows\Application\Command;

final class TriggerFlowCommand
{
    public function __construct(
        public readonly string  $flowId,
        public readonly array   $payload     = [],
        public readonly ?string $triggeredBy = null,
    ) {}
}
