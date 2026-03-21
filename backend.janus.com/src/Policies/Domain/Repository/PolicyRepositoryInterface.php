<?php

declare(strict_types=1);

namespace App\Policies\Domain\Repository;

use App\Policies\Domain\Entity\Policy;

interface PolicyRepositoryInterface
{
    public function save(Policy $policy, bool $flush = true): void;

    public function delete(Policy $policy): void;

    public function findById(string $id): ?Policy;

    public function findByName(string $name): ?Policy;

    /** @return Policy[] */
    public function findPaginated(int $limit, int $offset): array;

    public function count(): int;
}
