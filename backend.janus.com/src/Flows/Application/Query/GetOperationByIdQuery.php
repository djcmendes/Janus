<?php

declare(strict_types=1);

namespace App\Flows\Application\Query;

final class GetOperationByIdQuery
{
    public function __construct(public readonly string $id) {}
}
