<?php

/**
 * @file SaveVersionHandler.php
 *
 * Command handler that creates a new named Version snapshot for a collection item.
 *
 * @package App\Versions\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\SaveVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Exception\VersionAlreadyExistsException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

/**
 * Handles SaveVersionCommand by enforcing uniqueness on the collection+item+key triplet,
 * creating the Version domain entity, and persisting it via the repository.
 */
final class SaveVersionHandler
{
    /**
     * @param VersionRepositoryInterface $repository Storage and retrieval of Version records.
     */
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /**
     * Creates and persists a new Version, returning a DTO of the saved record.
     *
     * @param  SaveVersionCommand $command Payload carrying the snapshot data.
     * @return VersionDto                  DTO of the newly created Version.
     *
     * @throws VersionAlreadyExistsException When a version with the same collection+item+key already exists.
     */
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
