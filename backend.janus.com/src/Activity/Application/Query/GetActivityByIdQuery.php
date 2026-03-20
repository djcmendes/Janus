<?php

declare(strict_types=1);

namespace App\Activity\Application\Query;

final class GetActivityByIdQuery
{
    public function __construct(public readonly string $id) {}
}
