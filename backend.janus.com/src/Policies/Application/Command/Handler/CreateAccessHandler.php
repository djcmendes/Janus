<?php

declare(strict_types=1);

namespace App\Policies\Application\Command\Handler;

use App\Policies\Application\Command\CreateAccessCommand;
use App\Policies\Application\DTO\AccessDto;
use App\Policies\Domain\Entity\Access;
use App\Policies\Domain\Exception\AccessAlreadyExistsException;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use App\Policies\Domain\Repository\AccessRepositoryInterface;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;
use App\Roles\Domain\Repository\RoleRepositoryInterface;

final class CreateAccessHandler
{
    public function __construct(
        private readonly AccessRepositoryInterface  $accessRepository,
        private readonly PolicyRepositoryInterface  $policyRepository,
        private readonly RoleRepositoryInterface    $roleRepository,
    ) {}

    public function handle(CreateAccessCommand $command): AccessDto
    {
        $policy = $this->policyRepository->findById($command->policyId);
        if ($policy === null) {
            throw new PolicyNotFoundException($command->policyId);
        }

        $role = null;
        if ($command->roleId !== null) {
            $role = $this->roleRepository->findById($command->roleId);
            if ($role === null) {
                throw new \InvalidArgumentException(sprintf('Role "%s" not found.', $command->roleId));
            }
        }

        $roleId = $role ? (string) $role->getId() : 'public';
        $existing = $this->accessRepository->findByRoleAndPolicy($roleId, $command->policyId);
        if ($existing !== null) {
            throw new AccessAlreadyExistsException();
        }

        $access = new Access($role, $policy);
        $this->accessRepository->save($access);

        return AccessDto::fromEntity($access);
    }
}
