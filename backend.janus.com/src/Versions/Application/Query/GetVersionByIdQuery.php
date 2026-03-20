<?php

declare(strict_types=1);

namespace App\Versions\Application\Query;

final class GetVersionByIdQuery
{
    public function __construct(public readonly string $id) {}
}
