<?php

declare(strict_types=1);

namespace App\Policies\Application\Command\Handler;

use App\Policies\Application\Command\DeletePolicyCommand;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;

final class DeletePolicyHandler
{
    public function __construct(
        private readonly PolicyRepositoryInterface $repository,
    ) {}

    public function handle(DeletePolicyCommand $command): void
    {
        $policy = $this->repository->findById($command->id);
        if ($policy === null) {
            throw new PolicyNotFoundException($command->id);
        }

        $this->repository->delete($policy);
    }
}
