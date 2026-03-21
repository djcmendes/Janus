<?php

declare(strict_types=1);

namespace App\Translations\Application\Query;

final class GetTranslationsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $language = null,
        public readonly ?string $key      = null,
    ) {}
}
