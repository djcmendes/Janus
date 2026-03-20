<?php

declare(strict_types=1);

namespace App\Policies\Application\Command\Handler;

use App\Policies\Application\Command\UpdatePolicyCommand;
use App\Policies\Application\DTO\PolicyDto;
use App\Policies\Domain\Exception\PolicyAlreadyExistsException;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;

final class UpdatePolicyHandler
{
    public function __construct(
        private readonly PolicyRepositoryInterface $repository,
    ) {}

    public function handle(UpdatePolicyCommand $command): PolicyDto
    {
        $policy = $this->repository->findById($command->id);
        if ($policy === null) {
            throw new PolicyNotFoundException($command->id);
        }

        if ($command->name !== null) {
            $existing = $this->repository->findByName($command->name);
            if ($existing !== null && (string) $existing->getId() !== $command->id) {
                throw new PolicyAlreadyExistsException($command->name);
            }
            $policy->setName($command->name);
        }
        if ($command->description !== UpdatePolicyCommand::UNCHANGED) { $policy->setDescription($command->description); }
        if ($command->icon        !== UpdatePolicyCommand::UNCHANGED) { $policy->setIcon($command->icon); }
        if ($command->ipAccess    !== UpdatePolicyCommand::UNCHANGED) { $policy->setIpAccess($command->ipAccess); }
        if ($command->enforceTfa  !== null) { $policy->setEnforceTfa($command->enforceTfa); }
        if ($command->adminAccess !== null) { $policy->setAdminAccess($command->adminAccess); }
        if ($command->appAccess   !== null) { $policy->setAppAccess($command->appAccess); }

        $this->repository->save($policy);

        return PolicyDto::fromEntity($policy);
    }
}
