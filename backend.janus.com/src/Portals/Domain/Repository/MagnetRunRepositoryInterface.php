<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;

use App\Portals\Domain\Entity\MagnetRun;

interface MagnetRunRepositoryInterface
{
    public function save(MagnetRun $run, bool $flush = true): void;
    public function findById(string $id): ?MagnetRun;
    /** @return MagnetRun[] */
    public function findByMagnetId(string $magnetId, int $limit = 25, int $offset = 0): array;
    public function countByMagnetId(string $magnetId): int;
    public function findLatestByMagnetId(string $magnetId): ?MagnetRun;
}
