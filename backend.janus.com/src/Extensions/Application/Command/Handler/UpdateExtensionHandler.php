<?php

/**
 * @file UpdateExtensionHandler.php
 *
 * CQRS command handler — partially updates an existing Extension.
 *
 * @package App\Extensions\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler;

use App\Extensions\Application\Command\UpdateExtensionCommand;
use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

/**
 * Applies a partial update to an existing Extension and returns its updated DTO.
 *
 * Only fields whose value differs from UpdateExtensionCommand::UNCHANGED are written.
 */
final class UpdateExtensionHandler
{
    /**
     * @param ExtensionRepositoryInterface $repository Extension persistence gateway.
     */
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    /**
     * Applies the partial update and persists the result.
     *
     * @param  UpdateExtensionCommand   $command Carries id and partial field values.
     * @return ExtensionDto                       The updated extension as a serialisable read model.
     *
     * @throws ExtensionNotFoundException When no extension exists for the given UUID.
     */
    public function handle(UpdateExtensionCommand $command): ExtensionDto
    {
        $extension = $this->repository->findById($command->id);

        if ($extension === null) {
            throw new ExtensionNotFoundException($command->id);
        }

        if ($command->enabled !== UpdateExtensionCommand::UNCHANGED) {
            $extension->setEnabled((bool) $command->enabled);
        }
        if ($command->version !== UpdateExtensionCommand::UNCHANGED) {
            $extension->setVersion($command->version);
        }
        if ($command->meta !== UpdateExtensionCommand::UNCHANGED) {
            $extension->setMeta($command->meta);
        }

        $this->repository->save($extension);

        return ExtensionDto::fromEntity($extension);
    }
}
