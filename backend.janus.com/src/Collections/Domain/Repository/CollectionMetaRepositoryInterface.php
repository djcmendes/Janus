<?php

declare(strict_types=1);

namespace App\Collections\Domain\Repository;

use App\Collections\Domain\Entity\CollectionMeta;

interface CollectionMetaRepositoryInterface
{
    public function save(CollectionMeta $collection, bool $flush = true): void;
    public function delete(CollectionMeta $collection): void;
    public function findByName(string $name): ?CollectionMeta;

    /** @return CollectionMeta[] */
    public function findAll(int $limit, int $offset): array;
    public function count(array $criteria = []): int;
}
