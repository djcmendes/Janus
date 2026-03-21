<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Repository;

use App\Extensions\Domain\Entity\Extension;

interface ExtensionRepositoryInterface
{
    public function save(Extension $extension): void;

    public function delete(Extension $extension): void;

    public function findById(string $id): ?Extension;

    /** @return Extension[] */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $type    = null,
        ?bool   $enabled = null,
    ): array;

    public function countAll(
        ?string $type    = null,
        ?bool   $enabled = null,
    ): int;
}
