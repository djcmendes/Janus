<?php

declare(strict_types=1);

namespace App\Permissions\Application\Query\Handler;

use App\Permissions\Application\DTO\PermissionDto;
use App\Permissions\Application\Query\GetPermissionsQuery;
use App\Permissions\Domain\Repository\PermissionRepositoryInterface;

final class GetPermissionsHandler
{
    public function __construct(
        private readonly PermissionRepositoryInterface $repository,
    ) {}

    /** @return array{data: PermissionDto[], total: int} */
    public function handle(GetPermissionsQuery $query): array
    {
        $items = $query->policyId !== null
            ? $this->repository->findByPolicy($query->policyId, $query->limit, $query->offset)
            : $this->repository->findAll($query->limit, $query->offset);

        return [
            'data'  => array_map(PermissionDto::fromEntity(...), $items),
            'total' => $this->repository->count(),
        ];
    }
}
