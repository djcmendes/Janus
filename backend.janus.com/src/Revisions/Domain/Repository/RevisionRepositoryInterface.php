<?php

declare(strict_types=1);

namespace App\Revisions\Domain\Repository;

use App\Revisions\Domain\Entity\Revision;

interface RevisionRepositoryInterface
{
    public function record(Revision $revision): void;
    public function findById(string $id): ?Revision;
    public function findLatestForItem(string $collection, string $item): ?Revision;

    /** @return Revision[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $item       = null,
    ): array;

    public function countAll(
        ?string $collection = null,
        ?string $item       = null,
    ): int;
}
