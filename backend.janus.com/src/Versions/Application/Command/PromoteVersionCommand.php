<?php

declare(strict_types=1);

namespace App\Versions\Application\Command;

final class PromoteVersionCommand
{
    public function __construct(public readonly string $id) {}
}
