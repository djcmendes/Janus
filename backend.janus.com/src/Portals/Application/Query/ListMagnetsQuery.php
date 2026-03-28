<?php
declare(strict_types=1);
namespace App\Portals\Application\Query;

final class ListMagnetsQuery
{
    public function __construct(
        public readonly string $portalId,
        public readonly int    $limit  = 25,
        public readonly int    $offset = 0,
    ) {}
}
