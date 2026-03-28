<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;
use App\Portals\Domain\Entity\Portal;

interface PortalRepositoryInterface
{
    public function save(Portal $portal, bool $flush = true): void;
    public function delete(Portal $portal): void;
    public function findById(string $id): ?Portal;
    public function findByBaseRoute(string $route): ?Portal;
    /** @return Portal[] */
    public function findPaginated(int $limit, int $offset): array;
    public function countAll(): int;
}
