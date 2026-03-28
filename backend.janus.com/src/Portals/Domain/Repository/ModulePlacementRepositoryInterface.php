<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;
use App\Portals\Domain\Entity\ModulePlacement;
interface ModulePlacementRepositoryInterface
{
    public function save(ModulePlacement $placement, bool $flush = true): void;
    public function delete(ModulePlacement $placement): void;
    public function findById(string $id): ?ModulePlacement;
    /** @return ModulePlacement[] */
    public function findByPage(string $pageId): array;
    /** @return ModulePlacement[] */
    public function findByPageAndPosition(string $pageId, string $positionName): array;
}
