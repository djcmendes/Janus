<?php

declare(strict_types=1);

namespace App\Translations\Application\Command;

final class CreateTranslationCommand
{
    public function __construct(
        public readonly string $language,
        public readonly string $key,
        public readonly string $value,
    ) {}
}
