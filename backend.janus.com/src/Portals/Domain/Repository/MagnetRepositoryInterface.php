<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;

use App\Portals\Domain\Entity\Magnet;

interface MagnetRepositoryInterface
{
    public function save(Magnet $magnet, bool $flush = true): void;
    public function delete(Magnet $magnet): void;
    public function findById(string $id): ?Magnet;
    /** @return Magnet[] */
    public function findByPortalId(string $portalId): array;
    public function countActiveByPortalId(string $portalId): int;
}
