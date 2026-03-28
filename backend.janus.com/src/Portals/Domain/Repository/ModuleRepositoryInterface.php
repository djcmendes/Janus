<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;
use App\Portals\Domain\Entity\Module;
interface ModuleRepositoryInterface
{
    public function save(Module $module, bool $flush = true): void;
    public function delete(Module $module): void;
    public function findById(string $id): ?Module;
    /** @return Module[] */
    public function findPaginated(int $limit, int $offset, ?string $portalId = null): array;
    public function countAll(?string $portalId = null): int;
}
