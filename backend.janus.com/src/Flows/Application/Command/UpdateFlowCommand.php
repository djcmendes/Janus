<?php

declare(strict_types=1);

namespace App\Flows\Application\Command;

final class UpdateFlowCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string      $id,
        public readonly string|null $name,
        public readonly string|null $status,
        public readonly string|null $trigger,
        public readonly mixed       $triggerOptions,
        public readonly string|null $description,
    ) {}
}
