<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Enum;

enum ExtensionType: string
{
    case INTERFACE_  = 'interface';
    case ENDPOINT    = 'endpoint';
    case HOOK        = 'hook';
    case OPERATION   = 'operation';
    case DISPLAY     = 'display';
    case LAYOUT      = 'layout';
    case MODULE      = 'module';
    case PANEL       = 'panel';
}
