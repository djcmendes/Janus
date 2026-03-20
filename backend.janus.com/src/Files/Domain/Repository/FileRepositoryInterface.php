<?php

declare(strict_types=1);

namespace App\Files\Domain\Repository;

use App\Files\Domain\Entity\File;

interface FileRepositoryInterface
{
    public function save(File $file, bool $flush = true): void;
    public function delete(File $file): void;
    public function findById(string $id): ?File;

    /** @return File[] */
    public function findAll(int $limit, int $offset, ?string $folderId = null): array;
    public function countAll(?string $folderId = null): int;
}
