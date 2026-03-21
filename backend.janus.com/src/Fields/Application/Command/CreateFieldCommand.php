<?php

declare(strict_types=1);

namespace App\Fields\Application\Command;

final class CreateFieldCommand
{
    public function __construct(
        public readonly string  $collection,
        public readonly string  $field,
        public readonly string  $type,
        public readonly ?string $label     = null,
        public readonly ?string $note      = null,
        public readonly bool    $required  = false,
        public readonly bool    $readonly  = false,
        public readonly bool    $hidden    = false,
        public readonly int     $sortOrder = 0,
    ) {}
}
