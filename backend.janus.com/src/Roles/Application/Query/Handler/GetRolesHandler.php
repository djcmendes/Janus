<?php

declare(strict_types=1);

namespace App\Roles\Application\Query\Handler;

use App\Roles\Application\DTO\RoleDto;
use App\Roles\Application\Query\GetRolesQuery;
use App\Roles\Domain\Repository\RoleRepositoryInterface;

final class GetRolesHandler
{
    public function __construct(
        private readonly RoleRepositoryInterface $repository,
    ) {}

    /** @return array{data: RoleDto[], total: int} */
    public function handle(GetRolesQuery $query): array
    {
        $roles = $this->repository->findPaginated($query->limit, $query->offset);
        $total = $this->repository->count();

        return [
            'data'  => array_map(RoleDto::fromEntity(...), $roles),
            'total' => $total,
        ];
    }
}
