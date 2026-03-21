<?php

declare(strict_types=1);

namespace App\Roles\Application\Command\Handler;

use App\Roles\Application\Command\UpdateRoleCommand;
use App\Roles\Application\DTO\RoleDto;
use App\Roles\Domain\Exception\RoleAlreadyExistsException;
use App\Roles\Domain\Exception\RoleNotFoundException;
use App\Roles\Domain\Repository\RoleRepositoryInterface;

final class UpdateRoleHandler
{
    public function __construct(
        private readonly RoleRepositoryInterface $repository,
    ) {}

    public function handle(UpdateRoleCommand $command): RoleDto
    {
        $role = $this->repository->findById($command->id);

        if ($role === null) {
            throw new RoleNotFoundException($command->id);
        }

        if ($command->name !== null) {
            $existing = $this->repository->findByName($command->name);
            if ($existing !== null && (string) $existing->getId() !== $command->id) {
                throw new RoleAlreadyExistsException($command->name);
            }
            $role->setName($command->name);
        }
        if ($command->description !== UpdateRoleCommand::UNCHANGED) {
            $role->setDescription($command->description);
        }
        if ($command->icon !== UpdateRoleCommand::UNCHANGED) {
            $role->setIcon($command->icon);
        }
        if ($command->enforceTfa !== null) {
            $role->setEnforceTfa($command->enforceTfa);
        }
        if ($command->adminAccess !== null) {
            $role->setAdminAccess($command->adminAccess);
        }
        if ($command->appAccess !== null) {
            $role->setAppAccess($command->appAccess);
        }

        $this->repository->save($role);

        return RoleDto::fromEntity($role);
    }
}
