<?php

declare(strict_types=1);

namespace App\Policies\Application\Command\Handler;

use App\Policies\Application\Command\DeleteAccessCommand;
use App\Policies\Domain\Exception\AccessNotFoundException;
use App\Policies\Domain\Repository\AccessRepositoryInterface;

final class DeleteAccessHandler
{
    public function __construct(
        private readonly AccessRepositoryInterface $repository,
    ) {}

    public function handle(DeleteAccessCommand $command): void
    {
        $access = $this->repository->findById($command->id);
        if ($access === null) {
            throw new AccessNotFoundException($command->id);
        }

        $this->repository->delete($access);
    }
}
