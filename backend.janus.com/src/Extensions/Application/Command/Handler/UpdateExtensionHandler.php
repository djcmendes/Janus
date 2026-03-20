<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler;

use App\Extensions\Application\Command\UpdateExtensionCommand;
use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

final class UpdateExtensionHandler
{
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

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
