<?php

declare(strict_types=1);

namespace App\Roles\Application\Command\Handler;

use App\Roles\Application\Command\CreateRoleCommand;
use App\Roles\Application\DTO\RoleDto;
use App\Roles\Domain\Entity\Role;
use App\Roles\Domain\Exception\RoleAlreadyExistsException;
use App\Roles\Domain\Repository\RoleRepositoryInterface;

final class CreateRoleHandler
{
    public function __construct(
        private readonly RoleRepositoryInterface $repository,
    ) {}

    public function handle(CreateRoleCommand $command): RoleDto
    {
        if ($this->repository->findByName($command->name) !== null) {
            throw new RoleAlreadyExistsException($command->name);
        }

        $role = new Role($command->name);

        if ($command->description !== null) {
            $role->setDescription($command->description);
        }
        if ($command->icon !== null) {
            $role->setIcon($command->icon);
        }

        $role->setEnforceTfa($command->enforceTfa);
        $role->setAdminAccess($command->adminAccess);
        $role->setAppAccess($command->appAccess);

        $this->repository->save($role);

        return RoleDto::fromEntity($role);
    }
}
