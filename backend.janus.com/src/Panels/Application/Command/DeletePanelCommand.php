<?php

declare(strict_types=1);

namespace App\Panels\Application\Command;

final class DeletePanelCommand
{
    public function __construct(public readonly string $id) {}
}
