<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
enum ModuleType: string
{
    case MENU       = 'menu';
    case HTML       = 'html';
    case COLLECTION = 'collection';
    case SEARCH     = 'search';
    case CUSTOM     = 'custom';
}
