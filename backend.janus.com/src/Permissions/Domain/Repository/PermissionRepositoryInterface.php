<?php

declare(strict_types=1);

namespace App\Permissions\Domain\Repository;

use App\Permissions\Domain\Entity\Permission;

interface PermissionRepositoryInterface
{
    public function save(Permission $permission, bool $flush = true): void;

    public function delete(Permission $permission): void;

    public function findById(string $id): ?Permission;

    /** @return Permission[] */
    public function findAll(int $limit, int $offset): array;

    /** @return Permission[] */
    public function findByPolicy(string $policyId, int $limit, int $offset): array;

    public function count(): int;
}
