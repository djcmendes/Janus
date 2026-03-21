<?php

declare(strict_types=1);

namespace App\Deployments\Application\Command;

final class TriggerDeploymentCommand
{
    public function __construct(
        public readonly string  $providerId,
        public readonly ?string $triggeredBy = null,
    ) {}
}
