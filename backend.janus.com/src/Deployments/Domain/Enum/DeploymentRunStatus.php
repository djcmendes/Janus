<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Enum;

enum DeploymentRunStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILURE = 'failure';
}
