<?php

/**
 * @file DeleteExtensionHandler.php
 *
 * CQRS command handler — removes an Extension from the registry.
 *
 * @package App\Extensions\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler;

use App\Extensions\Application\Command\DeleteExtensionCommand;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

/**
 * Finds and deletes an existing Extension record.
 */
final class DeleteExtensionHandler
{
    /**
     * @param ExtensionRepositoryInterface $repository Extension persistence gateway.
     */
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    /**
     * Deletes the extension identified by the command's UUID.
     *
     * @param  DeleteExtensionCommand   $command Carries the UUID of the extension to delete.
     * @return void
     *
     * @throws ExtensionNotFoundException When no extension exists for the given UUID.
     */
    public function handle(DeleteExtensionCommand $command): void
    {
        $extension = $this->repository->findById($command->id);

        if ($extension === null) {
            throw new ExtensionNotFoundException($command->id);
        }

        $this->repository->delete($extension);
    }
}
