<?php

/**
 * @file DeleteVersionHandler.php
 *
 * Command handler that permanently removes a Version record.
 *
 * @package App\Versions\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\DeleteVersionCommand;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

/**
 * Handles DeleteVersionCommand by loading the Version and delegating removal to the repository.
 */
final class DeleteVersionHandler
{
    /**
     * @param VersionRepositoryInterface $repository Storage and retrieval of Version records.
     */
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /**
     * Removes the Version identified by command id from the store.
     *
     * @param  DeleteVersionCommand $command Payload carrying the UUID to remove.
     * @return void
     *
     * @throws VersionNotFoundException When no Version exists for the given id.
     */
    public function handle(DeleteVersionCommand $command): void
    {
        $version = $this->repository->findById($command->id);

        if ($version === null) {
            throw new VersionNotFoundException($command->id);
        }

        $this->repository->delete($version);
    }
}
