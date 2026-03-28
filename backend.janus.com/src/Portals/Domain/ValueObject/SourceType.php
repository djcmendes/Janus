<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;

enum SourceType: string
{
    case RSS     = 'rss';
    case API     = 'api';
    case WEBHOOK = 'webhook';
}
