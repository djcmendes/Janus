<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
enum PortalStatus: string
{
    case ACTIVE   = 'active';
    case DRAFT    = 'draft';
    case ARCHIVED = 'archived';
}
