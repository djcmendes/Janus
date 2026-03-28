<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
final class PortalListDto
{
    /** @param PortalDto[] $data */
    public function __construct(
        public readonly array $data,
        public readonly int   $total,
    ) {}
}
