<?php

declare(strict_types=1);

namespace App\Relations\Application\Command;

final class UpdateRelationCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string $manyCollection,
        public readonly string $manyField,
        public readonly mixed  $oneCollection      = self::UNCHANGED,
        public readonly mixed  $oneField           = self::UNCHANGED,
        public readonly mixed  $junctionCollection = self::UNCHANGED,
    ) {}
}
