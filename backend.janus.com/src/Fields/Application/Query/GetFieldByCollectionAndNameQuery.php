<?php

declare(strict_types=1);

namespace App\Fields\Application\Query;

final class GetFieldByCollectionAndNameQuery
{
    public function __construct(
        public readonly string $collection,
        public readonly string $field,
    ) {}
}
