<?php

declare(strict_types=1);

namespace App\Permissions\Application\Query\Handler;

use App\Permissions\Application\DTO\PermissionDto;
use App\Permissions\Application\Query\GetPermissionByIdQuery;
use App\Permissions\Domain\Exception\PermissionNotFoundException;
use App\Permissions\Domain\Repository\PermissionRepositoryInterface;

final class GetPermissionByIdHandler
{
    public function __construct(
        private readonly PermissionRepositoryInterface $repository,
    ) {}

    public function handle(GetPermissionByIdQuery $query): PermissionDto
    {
        $permission = $this->repository->findById($query->id);
        if ($permission === null) {
            throw new PermissionNotFoundException($query->id);
        }

        return PermissionDto::fromEntity($permission);
    }
}
