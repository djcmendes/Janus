<?php

declare(strict_types=1);

namespace App\Flows\Domain\Message;

final class RunFlowMessage
{
    public function __construct(
        public readonly string $flowId,
        public readonly array  $payload = [],
        public readonly ?string $triggeredBy = null,
    ) {}
}
