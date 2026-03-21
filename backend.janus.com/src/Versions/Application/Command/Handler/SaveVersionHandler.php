<?php

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\SaveVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Exception\VersionAlreadyExistsException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

final class SaveVersionHandler
{
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /** @throws VersionAlreadyExistsException */
    public function handle(SaveVersionCommand $command): VersionDto
    {
        $existing = $this->repository->findByCollectionItemAndKey(
            $command->collection,
            $command->item,
            $command->key,
        );

        if ($existing !== null) {
            throw new VersionAlreadyExistsException($command->collection, $command->item, $command->key);
        }

        $version = new Version(
            $command->collection,
            $command->item,
            $command->key,
            $command->data,
            $command->delta,
            $command->userId,
        );

        $this->repository->save($version);

        return VersionDto::fromEntity($version);
    }
}
