<?php

declare(strict_types=1);

namespace App\Collections\Application\Command;

final class UpdateCollectionCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string $name,
        public readonly ?string $label     = null,
        public readonly mixed  $icon       = self::UNCHANGED,
        public readonly mixed  $note       = self::UNCHANGED,
        public readonly ?bool  $hidden     = null,
        public readonly ?bool  $singleton  = null,
        public readonly mixed  $sortField  = self::UNCHANGED,
    ) {}
}
