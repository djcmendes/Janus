<?php

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\UpdateVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

final class UpdateVersionHandler
{
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /** @throws VersionNotFoundException */
    public function handle(UpdateVersionCommand $command): VersionDto
    {
        $version = $this->repository->findById($command->id);

        if ($version === null) {
            throw new VersionNotFoundException($command->id);
        }

        if ($command->key !== UpdateVersionCommand::UNCHANGED) {
            $version->setKey((string) $command->key);
        }

        if ($command->delta !== UpdateVersionCommand::UNCHANGED) {
            $version->setDelta(is_array($command->delta) ? $command->delta : null);
        }

        $this->repository->save($version);

        return VersionDto::fromEntity($version);
    }
}
