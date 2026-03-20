<?php

declare(strict_types=1);

namespace App\Dashboards\Domain\Exception;

final class DashboardNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Dashboard '{$id}' not found.");
    }
}
