<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
enum ComponentType: string
{
    case CONTENT         = 'content';
    case COLLECTION_LIST = 'collection-list';
    case FORM            = 'form';
    case REDIRECT        = 'redirect';
}
