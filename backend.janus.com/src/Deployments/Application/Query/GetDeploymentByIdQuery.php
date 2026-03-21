<?php

declare(strict_types=1);

namespace App\Deployments\Application\Query;

final class GetDeploymentByIdQuery
{
    public function __construct(public readonly string $id) {}
}
