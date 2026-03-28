<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
enum PageStatus: string
{
    case PUBLISHED = 'published';
    case DRAFT     = 'draft';
    case ARCHIVED  = 'archived';
}
