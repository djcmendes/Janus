<?php

declare(strict_types=1);

namespace App\Flows\Application\Command;

final class UpdateOperationCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string      $id,
        public readonly string|null $name,
        public readonly string|null $type,
        public readonly mixed       $options,
        public readonly string|null $resolve,
        public readonly string|null $nextSuccess,
        public readonly string|null $nextFailure,
        public readonly int|string  $sortOrder,
    ) {}
}
