<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Command;

final class CreateDashboardCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $icon   = null,
        public readonly ?string $note   = null,
        public readonly ?string $userId = null,
    ) {}
}
