<?php

declare(strict_types=1);

namespace App\Roles\Application\Command\Handler;

use App\Roles\Application\Command\DeleteRoleCommand;
use App\Roles\Domain\Exception\RoleNotFoundException;
use App\Roles\Domain\Repository\RoleRepositoryInterface;

final class DeleteRoleHandler
{
    public function __construct(
        private readonly RoleRepositoryInterface $repository,
    ) {}

    public function handle(DeleteRoleCommand $command): void
    {
        $role = $this->repository->findById($command->id);

        if ($role === null) {
            throw new RoleNotFoundException($command->id);
        }

        // Roles are configuration entities — hard delete, no soft delete needed
        $this->repository->delete($role);
    }
}
