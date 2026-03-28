<?php

declare(strict_types=1);

namespace App\Collections\Application\Command;

final class CreateCollectionCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $label          = null,
        public readonly ?string $icon           = null,
        public readonly ?string $note           = null,
        public readonly bool    $hidden         = false,
        public readonly bool    $singleton      = false,
        public readonly ?string $sortField      = null,
        public readonly string  $primaryKeyField = 'id',
        public readonly string  $primaryKeyType  = 'uuid',
    ) {}
}
