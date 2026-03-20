<?php

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\DeleteVersionCommand;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

final class DeleteVersionHandler
{
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /** @throws VersionNotFoundException */
    public function handle(DeleteVersionCommand $command): void
    {
        $version = $this->repository->findById($command->id);

        if ($version === null) {
            throw new VersionNotFoundException($command->id);
        }

        $this->repository->delete($version);
    }
}
