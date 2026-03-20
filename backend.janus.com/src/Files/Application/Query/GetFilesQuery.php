<?php

declare(strict_types=1);

namespace App\Files\Application\Query;

final class GetFilesQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $folderId = null,
    ) {}
}
