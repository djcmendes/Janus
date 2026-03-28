<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;
use App\Portals\Domain\Entity\ComponentDefinition;
interface ComponentRepositoryInterface
{
    public function save(ComponentDefinition $component, bool $flush = true): void;
    public function delete(ComponentDefinition $component): void;
    public function findById(string $id): ?ComponentDefinition;
    /** @return ComponentDefinition[] */
    public function findPaginated(int $limit, int $offset): array;
    public function countAll(): int;
}
