<?php

declare(strict_types=1);

namespace App\Files\Application\Command;

final class CreateFolderCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $parentId = null,
    ) {}
}
