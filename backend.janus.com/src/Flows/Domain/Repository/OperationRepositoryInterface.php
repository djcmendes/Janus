<?php

declare(strict_types=1);

namespace App\Flows\Domain\Repository;

use App\Flows\Domain\Entity\Operation;

interface OperationRepositoryInterface
{
    public function save(Operation $operation): void;

    public function delete(Operation $operation): void;

    public function findById(string $id): ?Operation;

    /** @return Operation[] */
    public function findPaginated(int $limit, int $offset, ?string $flowId = null): array;

    public function countAll(?string $flowId = null): int;

    public function deleteByFlow(string $flowId): void;
}
