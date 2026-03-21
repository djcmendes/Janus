<?php

declare(strict_types=1);

namespace App\Permissions\Application\Query;

final class GetPermissionByIdQuery
{
    public function __construct(public readonly string $id) {}
}
