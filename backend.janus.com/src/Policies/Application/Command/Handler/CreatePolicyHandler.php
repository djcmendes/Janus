<?php

declare(strict_types=1);

namespace App\Policies\Application\Command\Handler;

use App\Policies\Application\Command\CreatePolicyCommand;
use App\Policies\Application\DTO\PolicyDto;
use App\Policies\Domain\Entity\Policy;
use App\Policies\Domain\Exception\PolicyAlreadyExistsException;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;

final class CreatePolicyHandler
{
    public function __construct(
        private readonly PolicyRepositoryInterface $repository,
    ) {}

    public function handle(CreatePolicyCommand $command): PolicyDto
    {
        if ($this->repository->findByName($command->name) !== null) {
            throw new PolicyAlreadyExistsException($command->name);
        }

        $policy = new Policy($command->name);

        if ($command->description !== null) { $policy->setDescription($command->description); }
        if ($command->icon !== null)        { $policy->setIcon($command->icon); }

        $policy->setEnforceTfa($command->enforceTfa);
        $policy->setAdminAccess($command->adminAccess);
        $policy->setAppAccess($command->appAccess);
        $policy->setIpAccess($command->ipAccess);

        $this->repository->save($policy);

        return PolicyDto::fromEntity($policy);
    }
}
