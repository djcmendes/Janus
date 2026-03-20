<?php

declare(strict_types=1);

namespace App\Flows\Application\Query;

final class GetFlowByIdQuery
{
    public function __construct(public readonly string $id) {}
}
