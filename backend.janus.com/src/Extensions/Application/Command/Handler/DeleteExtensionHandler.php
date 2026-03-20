<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler;

use App\Extensions\Application\Command\DeleteExtensionCommand;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

final class DeleteExtensionHandler
{
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    public function handle(DeleteExtensionCommand $command): void
    {
        $extension = $this->repository->findById($command->id);

        if ($extension === null) {
            throw new ExtensionNotFoundException($command->id);
        }

        $this->repository->delete($extension);
    }
}
