<?php

declare(strict_types=1);

namespace App\Permissions\Domain\Enum;

enum PermissionAction: string
{
    case CREATE = 'create';
    case READ   = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case SHARE  = 'share';
    case SORT   = 'sort';
}
