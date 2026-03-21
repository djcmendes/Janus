<?php

declare(strict_types=1);

namespace App\Permissions\Application\Command;

final class DeletePermissionCommand
{
    public function __construct(public readonly string $id) {}
}
