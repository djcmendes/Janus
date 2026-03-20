<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query;

final class GetExtensionsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $type    = null,
        public readonly ?bool   $enabled = null,
    ) {}
}
