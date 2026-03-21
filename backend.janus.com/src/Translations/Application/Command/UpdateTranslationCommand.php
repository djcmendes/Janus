<?php

declare(strict_types=1);

namespace App\Translations\Application\Command;

final class UpdateTranslationCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $value,
    ) {}
}
