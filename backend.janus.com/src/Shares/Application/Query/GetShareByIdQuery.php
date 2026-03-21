<?php

declare(strict_types=1);

namespace App\Shares\Application\Query;

final class GetShareByIdQuery
{
    public function __construct(public readonly string $id) {}
}
