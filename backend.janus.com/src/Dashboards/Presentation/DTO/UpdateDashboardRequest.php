<?php

declare(strict_types=1);

namespace App\Dashboards\Presentation\DTO;

final class UpdateDashboardRequest
{
    public string|null $name = '__UNCHANGED__';
    public string|null $icon = '__UNCHANGED__';
    public string|null $note = '__UNCHANGED__';
}
