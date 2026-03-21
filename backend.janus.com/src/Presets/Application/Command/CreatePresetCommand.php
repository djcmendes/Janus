<?php

declare(strict_types=1);

namespace App\Presets\Application\Command;

final class CreatePresetCommand
{
    public function __construct(
        public readonly ?string $collection,
        public readonly ?string $layout,
        public readonly ?array  $layoutOptions,
        public readonly ?array  $layoutQuery,
        public readonly ?array  $filter,
        public readonly ?string $search,
        public readonly ?string $bookmark,
        public readonly ?string $userId,
    ) {}
}
