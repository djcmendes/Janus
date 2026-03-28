<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;
use App\Portals\Domain\Entity\LayoutTemplate;
interface LayoutTemplateRepositoryInterface
{
    public function save(LayoutTemplate $template, bool $flush = true): void;
    public function delete(LayoutTemplate $template): void;
    public function findById(string $id): ?LayoutTemplate;
    /** @return LayoutTemplate[] */
    public function findPaginated(int $limit, int $offset): array;
    public function countAll(): int;
}
