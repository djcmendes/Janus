<?php

declare(strict_types=1);

namespace App\Fields\Application\Command;

final class UpdateFieldCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string  $collection,
        public readonly string  $field,
        public readonly mixed   $label     = self::UNCHANGED,
        public readonly mixed   $note      = self::UNCHANGED,
        public readonly ?bool   $required  = null,
        public readonly ?bool   $readonly  = null,
        public readonly ?bool   $hidden    = null,
        public readonly ?int    $sortOrder = null,
        public readonly mixed   $interface = self::UNCHANGED,
        public readonly mixed   $options   = self::UNCHANGED,
    ) {}
}
