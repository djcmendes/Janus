<?php

declare(strict_types=1);

namespace App\Permissions\Application\Command\Handler;

use App\Permissions\Application\Command\DeletePermissionCommand;
use App\Permissions\Domain\Exception\PermissionNotFoundException;
use App\Permissions\Domain\Repository\PermissionRepositoryInterface;

final class DeletePermissionHandler
{
    public function __construct(
        private readonly PermissionRepositoryInterface $repository,
    ) {}

    public function handle(DeletePermissionCommand $command): void
    {
        $permission = $this->repository->findById($command->id);
        if ($permission === null) {
            throw new PermissionNotFoundException($command->id);
        }

        $this->repository->delete($permission);
    }
}
