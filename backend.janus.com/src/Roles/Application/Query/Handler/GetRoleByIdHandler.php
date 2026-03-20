<?php

declare(strict_types=1);

namespace App\Roles\Application\Query\Handler;

use App\Roles\Application\DTO\RoleDto;
use App\Roles\Application\Query\GetRoleByIdQuery;
use App\Roles\Domain\Exception\RoleNotFoundException;
use App\Roles\Domain\Repository\RoleRepositoryInterface;

final class GetRoleByIdHandler
{
    public function __construct(
        private readonly RoleRepositoryInterface $repository,
    ) {}

    public function handle(GetRoleByIdQuery $query): RoleDto
    {
        $role = $this->repository->findById($query->id);

        if ($role === null) {
            throw new RoleNotFoundException($query->id);
        }

        return RoleDto::fromEntity($role);
    }
}
