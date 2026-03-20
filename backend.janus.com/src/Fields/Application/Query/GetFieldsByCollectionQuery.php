<?php

declare(strict_types=1);

namespace App\Fields\Application\Query;

final class GetFieldsByCollectionQuery
{
    public function __construct(public readonly string $collection) {}
}
