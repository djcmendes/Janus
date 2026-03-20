<?php

declare(strict_types=1);

namespace App\Activity\Domain\Repository;

use App\Activity\Domain\Entity\Activity;

interface ActivityRepositoryInterface
{
    public function record(Activity $activity): void;
    public function findById(string $id): ?Activity;

    /**
     * @return Activity[]
     */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): array;

    public function countAll(
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): int;
}
