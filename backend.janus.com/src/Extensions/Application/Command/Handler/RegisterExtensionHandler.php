<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler;

use App\Extensions\Application\Command\RegisterExtensionCommand;
use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

final class RegisterExtensionHandler
{
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

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
