<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Enum;

enum DeploymentProviderType: string
{
    case WEBHOOK = 'webhook';
    case NETLIFY = 'netlify';
    case VERCEL  = 'vercel';
    case CUSTOM  = 'custom';
}
