<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query;

final class GetExtensionByIdQuery
{
    public function __construct(public readonly string $id) {}
}
