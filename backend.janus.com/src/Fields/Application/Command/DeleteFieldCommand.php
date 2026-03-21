<?php

declare(strict_types=1);

namespace App\Fields\Application\Command;

final class DeleteFieldCommand
{
    public function __construct(
        public readonly string $collection,
        public readonly string $field,
    ) {}
}
