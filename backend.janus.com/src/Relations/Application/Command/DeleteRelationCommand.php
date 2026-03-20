<?php

declare(strict_types=1);

namespace App\Relations\Application\Command;

final class DeleteRelationCommand
{
    public function __construct(
        public readonly string $manyCollection,
        public readonly string $manyField,
    ) {}
}
