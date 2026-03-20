<?php

declare(strict_types=1);

namespace App\Flows\Domain\Enum;

enum FlowStatus: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
}
