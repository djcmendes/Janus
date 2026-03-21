<?php

declare(strict_types=1);

namespace App\Relations\Application\Command;

final class CreateRelationCommand
{
    public function __construct(
        public readonly string  $manyCollection,
        public readonly string  $manyField,
        public readonly ?string $oneCollection      = null,
        public readonly ?string $oneField           = null,
        public readonly ?string $junctionCollection = null,
    ) {}
}
