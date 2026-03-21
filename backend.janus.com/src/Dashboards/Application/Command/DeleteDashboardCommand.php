<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Command;

final class DeleteDashboardCommand
{
    public function __construct(public readonly string $id) {}
}
