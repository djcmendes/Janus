<?php

declare(strict_types=1);

namespace App\Fields\Domain\Repository;

use App\Fields\Domain\Entity\FieldMeta;

interface FieldMetaRepositoryInterface
{
    public function save(FieldMeta $field, bool $flush = true): void;
    public function delete(FieldMeta $field): void;
    public function findByCollectionAndField(string $collection, string $field): ?FieldMeta;

    /** @return FieldMeta[] */
    public function findByCollection(string $collection): array;

    /** @return FieldMeta[] */
    public function findAll(int $limit, int $offset): array;
    public function countAll(): int;

    /** Delete all fields belonging to a collection */
    public function deleteByCollection(string $collection): void;
}
