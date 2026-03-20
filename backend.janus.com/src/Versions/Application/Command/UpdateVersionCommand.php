<?php

declare(strict_types=1);

namespace App\Versions\Application\Command;

final class UpdateVersionCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string $id,
        public readonly mixed  $key   = self::UNCHANGED,
        public readonly mixed  $delta = self::UNCHANGED,
    ) {}
}
