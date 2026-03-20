<?php

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\PromoteVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use App\Versions\Domain\Service\VersionService;

final class PromoteVersionHandler
{
    public function __construct(
        private readonly VersionRepositoryInterface $repository,
        private readonly VersionService             $versionService,
    ) {}

    /**
     * Restores the version's data snapshot into the live item row.
     *
     * @throws VersionNotFoundException
     * @throws \RuntimeException when the live item row is not found
     */
    public function handle(PromoteVersionCommand $command): VersionDto
    {
        $version = $this->repository->findById($command->id);

        if ($version === null) {
            throw new VersionNotFoundException($command->id);
        }

        $this->versionService->promote($version);

        return VersionDto::fromEntity($version);
    }
}
