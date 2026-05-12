<?php

/**
 * @file UpdateVersionHandler.php
 *
 * Command handler that applies partial updates to an existing Version record.
 *
 * @package App\Versions\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler;

use App\Versions\Application\Command\UpdateVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

/**
 * Handles UpdateVersionCommand by loading the Version, applying only the fields
 * that differ from the UNCHANGED sentinel, and persisting the result.
 */
final class UpdateVersionHandler
{
    /**
     * @param VersionRepositoryInterface $repository Storage and retrieval of Version records.
     */
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /**
     * Applies partial mutations to the Version identified by command id and returns the updated DTO.
     *
     * @param  UpdateVersionCommand $command Payload with the id and optional new field values.
     * @return VersionDto                    DTO of the updated Version.
     *
     * @throws VersionNotFoundException When no Version exists for the given id.
     */
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
