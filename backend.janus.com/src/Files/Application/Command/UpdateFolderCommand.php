<?php

declare(strict_types=1);

namespace App\Files\Application\Command;

final class UpdateFolderCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string $id,
        public readonly ?string $name     = null,
        public readonly mixed   $parentId = self::UNCHANGED,
    ) {}
}
