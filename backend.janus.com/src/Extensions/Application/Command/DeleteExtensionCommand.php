<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command;

final class DeleteExtensionCommand
{
    public function __construct(public readonly string $id) {}
}
