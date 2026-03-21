<?php

declare(strict_types=1);

namespace App\Permissions\Application\Command\Handler;

use App\Permissions\Application\Command\CreatePermissionCommand;
use App\Permissions\Application\DTO\PermissionDto;
use App\Permissions\Domain\Entity\Permission;
use App\Permissions\Domain\Enum\PermissionAction;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;
use App\Permissions\Domain\Repository\PermissionRepositoryInterface;

final class CreatePermissionHandler
{
    public function __construct(
        private readonly PermissionRepositoryInterface $repository,
        private readonly PolicyRepositoryInterface     $policyRepository,
    ) {}

    public function handle(CreatePermissionCommand $command): PermissionDto
    {
        $policy = $this->policyRepository->findById($command->policyId);
        if ($policy === null) {
            throw new PolicyNotFoundException($command->policyId);
        }

        $action = PermissionAction::tryFrom($command->action);
        if ($action === null) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid action "%s". Allowed: %s.',
                $command->action,
                implode(', ', array_column(PermissionAction::cases(), 'value'))
            ));
        }

        $permission = new Permission($policy, $action);
        $permission->setCollection($command->collection);
        $permission->setFields($command->fields);
        $permission->setPermissionsFilter($command->permissionsFilter);
        $permission->setValidation($command->validation);
        $permission->setPresets($command->presets);

        $this->repository->save($permission);

        return PermissionDto::fromEntity($permission);
    }
}
