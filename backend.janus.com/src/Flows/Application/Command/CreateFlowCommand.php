<?php

declare(strict_types=1);

namespace App\Flows\Application\Command;

final class CreateFlowCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $status,
        public readonly string  $trigger,
        public readonly ?array  $triggerOptions = null,
        public readonly ?string $userId         = null,
        public readonly ?string $description    = null,
    ) {}
}
