<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;

enum MagnetStatus: string
{
    case ACTIVE  = 'active';
    case PAUSED  = 'paused';
    case ARCHIVED = 'archived';
}
