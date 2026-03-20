<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command;

final class UpdateExtensionCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string      $id,
        public readonly bool|string $enabled,
        public readonly string|null $version,
        public readonly mixed       $meta,
    ) {}
}
