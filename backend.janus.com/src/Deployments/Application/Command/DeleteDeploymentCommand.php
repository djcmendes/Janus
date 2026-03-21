<?php

declare(strict_types=1);

namespace App\Deployments\Application\Command;

final class DeleteDeploymentCommand
{
    public function __construct(public readonly string $id) {}
}
