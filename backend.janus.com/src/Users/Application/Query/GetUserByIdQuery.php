<?php

declare(strict_types=1);

namespace App\Users\Application\Query;

final class GetUserByIdQuery
{
    public function __construct(
        public readonly string $id,
    ) {}
}
