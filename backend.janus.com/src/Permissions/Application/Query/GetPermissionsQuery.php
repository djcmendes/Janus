<?php

declare(strict_types=1);

namespace App\Permissions\Application\Query;

final class GetPermissionsQuery
{
    public function __construct(
        public readonly int     $limit    = 25,
        public readonly int     $offset   = 0,
        public readonly ?string $policyId = null,
    ) {}
}
