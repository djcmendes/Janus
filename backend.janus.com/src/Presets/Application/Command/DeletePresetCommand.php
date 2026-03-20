<?php

declare(strict_types=1);

namespace App\Presets\Application\Command;

final class DeletePresetCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $requestingUserId,
        public readonly bool   $isAdmin = false,
    ) {}
}
