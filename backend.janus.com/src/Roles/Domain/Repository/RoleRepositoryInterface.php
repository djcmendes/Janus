<?php

declare(strict_types=1);

namespace App\Roles\Domain\Repository;

use App\Roles\Domain\Entity\Role;

interface RoleRepositoryInterface
{
    public function save(Role $role, bool $flush = true): void;

    public function findById(string $id): ?Role;

    public function findByName(string $name): ?Role;

    /** @return Role[] */
    public function findPaginated(int $limit, int $offset): array;

    public function count(): int;

    public function delete(Role $role): void;
}
