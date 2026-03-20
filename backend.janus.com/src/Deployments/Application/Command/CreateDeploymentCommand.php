<?php

declare(strict_types=1);

namespace App\Deployments\Application\Command;

final class CreateDeploymentCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $url,
        public readonly ?array  $options  = null,
        public readonly bool    $isActive = true,
    ) {}
}
