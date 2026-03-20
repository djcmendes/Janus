<?php

declare(strict_types=1);

namespace App\Policies\Domain\Repository;

use App\Policies\Domain\Entity\Access;

interface AccessRepositoryInterface
{
    public function save(Access $access, bool $flush = true): void;

    public function delete(Access $access): void;

    public function findById(string $id): ?Access;

    public function findByRoleAndPolicy(string $roleId, string $policyId): ?Access;

    /** @return Access[] */
    public function findAll(int $limit, int $offset): array;

    public function count(): int;
}
