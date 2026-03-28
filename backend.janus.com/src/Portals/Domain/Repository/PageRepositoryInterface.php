<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;
use App\Portals\Domain\Entity\Page;
interface PageRepositoryInterface
{
    public function save(Page $page, bool $flush = true): void;
    public function delete(Page $page): void;
    public function findById(string $id): ?Page;
    /** @return Page[] */
    public function findByPortalId(string $portalId): array;
    /** @return Page[] */
    public function findChildren(string $parentId): array;
    public function countByPortal(string $portalId): int;
    public function countByPortalAndStatus(string $portalId, string $status): int;
}
