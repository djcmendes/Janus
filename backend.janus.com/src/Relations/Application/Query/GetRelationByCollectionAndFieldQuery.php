<?php

declare(strict_types=1);

namespace App\Relations\Application\Query;

final class GetRelationByCollectionAndFieldQuery
{
    public function __construct(
        public readonly string $collection,
        public readonly string $field,
    ) {}
}
