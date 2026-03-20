<?php

declare(strict_types=1);

namespace App\Files\Domain\Repository;

use App\Files\Domain\Entity\Folder;

interface FolderRepositoryInterface
{
    public function save(Folder $folder, bool $flush = true): void;
    public function delete(Folder $folder): void;
    public function findById(string $id): ?Folder;

    /** @return Folder[] */
    public function findAll(int $limit, int $offset): array;
    public function count(array $criteria = []): int;
}
