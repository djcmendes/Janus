<?php

declare(strict_types=1);

namespace App\Items\Application\Command;

final class CreateItemCommand
{
    public function __construct(
        public readonly string $collection,
        public readonly array  $data,
    ) {}
}
