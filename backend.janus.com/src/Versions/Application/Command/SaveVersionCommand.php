<?php

declare(strict_types=1);

namespace App\Versions\Application\Command;

final class SaveVersionCommand
{
    public function __construct(
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $key,
        public readonly array   $data,
        public readonly ?array  $delta  = null,
        public readonly ?string $userId = null,
    ) {}
}
