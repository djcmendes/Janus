<?php

declare(strict_types=1);

namespace App\Permissions\Application\Command\Handler;

use App\Permissions\Application\Command\UpdatePermissionCommand;
use App\Permissions\Application\DTO\PermissionDto;
use App\Permissions\Domain\Enum\PermissionAction;
use App\Permissions\Domain\Exception\PermissionNotFoundException;
use App\Permissions\Domain\Repository\PermissionRepositoryInterface;

final class UpdatePermissionHandler
{
    public function __construct(
        private readonly PermissionRepositoryInterface $repository,
    ) {}

    public function handle(UpdatePermissionCommand $command): PermissionDto
    {
        $permission = $this->repository->findById($command->id);
        if ($permission === null) {
            throw new PermissionNotFoundException($command->id);
        }

        if ($command->action !== null) {
            $action = PermissionAction::tryFrom($command->action);
            if ($action === null) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid action "%s". Allowed: %s.',
                    $command->action,
                    implode(', ', array_column(PermissionAction::cases(), 'value'))
                ));
            }
            $permission->setAction($action);
        }

        if ($command->collection        !== UpdatePermissionCommand::UNCHANGED) { $permission->setCollection($command->collection); }
        if ($command->fields            !== UpdatePermissionCommand::UNCHANGED) { $permission->setFields($command->fields); }
        if ($command->permissionsFilter !== UpdatePermissionCommand::UNCHANGED) { $permission->setPermissionsFilter($command->permissionsFilter); }
        if ($command->validation        !== UpdatePermissionCommand::UNCHANGED) { $permission->setValidation($command->validation); }
        if ($command->presets           !== UpdatePermissionCommand::UNCHANGED) { $permission->setPresets($command->presets); }

        $this->repository->save($permission);

        return PermissionDto::fromEntity($permission);
    }
}
