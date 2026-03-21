<?php

declare(strict_types=1);

namespace App\Schema\Application\Command;

final class ApplySchemaCommand
{
    public function __construct(
        /** Full snapshot array (version + collections + relations) */
        public readonly array $snapshot,
        /** When true, collections/fields/relations absent from the snapshot are deleted */
        public readonly bool  $force = false,
    ) {}
}
