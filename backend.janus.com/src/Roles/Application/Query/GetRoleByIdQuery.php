<?php

declare(strict_types=1);

namespace App\Roles\Application\Query;

final class GetRoleByIdQuery
{
    public function __construct(
        public readonly string $id,
    ) {}
}
