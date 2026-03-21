<?php

declare(strict_types=1);

namespace App\Items\Application\Command;

final class UpdateItemCommand
{
    public function __construct(
        public readonly string $collection,
        public readonly string $id,
        public readonly array  $data,
    ) {}
}
