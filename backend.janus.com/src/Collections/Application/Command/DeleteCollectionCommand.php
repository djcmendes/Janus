<?php

declare(strict_types=1);

namespace App\Collections\Application\Command;

final class DeleteCollectionCommand
{
    public function __construct(public readonly string $name) {}
}
