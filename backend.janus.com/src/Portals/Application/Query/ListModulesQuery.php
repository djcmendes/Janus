<?php
declare(strict_types=1);
namespace App\Portals\Application\Query;
final class ListModulesQuery
{
    public function __construct(
        public readonly int     $limit    = 25,
        public readonly int     $offset   = 0,
        public readonly ?string $portalId = null,
    ) {}
}
