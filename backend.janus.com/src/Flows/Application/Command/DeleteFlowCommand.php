<?php

declare(strict_types=1);

namespace App\Flows\Application\Command;

final class DeleteFlowCommand
{
    public function __construct(public readonly string $id) {}
}
