<?php

declare(strict_types=1);

namespace App\Roles\Application\Command;

final class DeleteRoleCommand
{
    public function __construct(
        public readonly string $id,
    ) {}
}
