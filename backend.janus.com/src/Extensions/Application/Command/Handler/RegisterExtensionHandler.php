<?php

/**
 * @file RegisterExtensionHandler.php
 *
 * CQRS command handler — registers a new Extension in the registry.
 *
 * @package App\Extensions\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler;

use App\Extensions\Application\Command\RegisterExtensionCommand;
use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

/**
 * Creates and persists a new Extension, then returns its read model DTO.
 */
final class RegisterExtensionHandler
{
    /**
     * @param ExtensionRepositoryInterface $repository Extension persistence gateway.
     */
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    /**
     * Registers a new extension and returns its DTO.
     *
     * @param  RegisterExtensionCommand $command Carries name, type, version, enabled, description, meta.
     * @return ExtensionDto                       The created extension as a serialisable read model.
     */
    public function handle(RegisterExtensionCommand $command): ExtensionDto
    {
        $extension = new Extension(
            $command->name,
            ExtensionType::from($command->type),
            $command->version,
            $command->enabled,
            $command->description,
            $command->meta,
        );

        $this->repository->save($extension);

        return ExtensionDto::fromEntity($extension);
    }
}
