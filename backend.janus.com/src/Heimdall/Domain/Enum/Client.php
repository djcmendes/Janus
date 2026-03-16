<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Enum;

enum Client: string
{
    case ANDROID = 'android';
    case IOS     = 'ios';
    case WEB     = 'web';
    case CLI     = 'cli';
}
