<?php

declare(strict_types=1);

namespace App\Versions\Domain\Repository;

use App\Versions\Domain\Entity\Version;

interface VersionRepositoryInterface
{
    public function save(Version $version, bool $flush = true): void;
    public function delete(Version $version): void;
    public function findById(string $id): ?Version;
    public function findByCollectionItemAndKey(string $collection, string $item, string $key): ?Version;

    /** @return Version[] */
    public function findPaginated(int $limit, int $offset, ?string $collection = null, ?string $item = null): array;
    public function countAll(?string $collection = null, ?string $item = null): int;
}
