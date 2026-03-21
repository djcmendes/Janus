<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Command;

final class UpdateDashboardCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string       $id,
        public readonly string|null  $name,
        public readonly string|null  $icon,
        public readonly string|null  $note,
    ) {}
}
