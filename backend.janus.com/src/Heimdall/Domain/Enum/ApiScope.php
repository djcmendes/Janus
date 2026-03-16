<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Enum;

enum ApiScope: string
{
    case LOCAL         = 'local';
    case PUBLIC        = 'public';
    case AUTHENTICATED = 'authenticated';
}
