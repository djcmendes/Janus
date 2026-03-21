<?php

declare(strict_types=1);

namespace App\Flows\Domain\Repository;

use App\Flows\Domain\Entity\Flow;

interface FlowRepositoryInterface
{
    public function save(Flow $flow): void;

    public function delete(Flow $flow): void;

    public function findById(string $id): ?Flow;

    /** @return Flow[] */
    public function findPaginated(int $limit, int $offset, ?string $status = null): array;

    public function countAll(?string $status = null): int;
}
