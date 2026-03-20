<?php

declare(strict_types=1);

namespace App\Flows\Application\Command;

final class CreateOperationCommand
{
    public function __construct(
        public readonly string  $flowId,
        public readonly string  $name,
        public readonly string  $type,
        public readonly ?array  $options     = null,
        public readonly ?string $resolve     = null,
        public readonly ?string $nextSuccess = null,
        public readonly ?string $nextFailure = null,
        public readonly int     $sortOrder   = 0,
    ) {}
}
