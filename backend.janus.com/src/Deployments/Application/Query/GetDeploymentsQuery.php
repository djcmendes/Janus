<?php

declare(strict_types=1);

namespace App\Deployments\Application\Query;

final class GetDeploymentsQuery
{
    public function __construct(
        public readonly int  $limit,
        public readonly int  $offset,
    ) {}
}
