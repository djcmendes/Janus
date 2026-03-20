<?php

declare(strict_types=1);

namespace App\Relations\Domain\Repository;

use App\Relations\Domain\Entity\Relation;

interface RelationRepositoryInterface
{
    public function save(Relation $relation, bool $flush = true): void;
    public function delete(Relation $relation): void;
    public function findByCollectionAndField(string $manyCollection, string $manyField): ?Relation;

    /** @return Relation[] */
    public function findAll(int $limit, int $offset): array;
    public function count(array $criteria = []): int;

    /** Delete all relations belonging to a collection (either side) */
    public function deleteByCollection(string $collection): void;
}
