<?php

declare(strict_types=1);

namespace App\Files\Application\Command\Handler;

use App\Files\Application\Command\UpdateFolderCommand;
use App\Files\Application\DTO\FolderDto;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Domain\Repository\FolderRepositoryInterface;

final class UpdateFolderHandler
{
    public function __construct(private readonly FolderRepositoryInterface $repository) {}

    /** @throws FolderNotFoundException */
    public function handle(UpdateFolderCommand $command): FolderDto
    {
        $folder = $this->repository->findById($command->id);

        if ($folder === null) {
            throw new FolderNotFoundException($command->id);
        }

        if ($command->name !== null) {
            $folder->setName($command->name);
        }

        if ($command->parentId !== UpdateFolderCommand::UNCHANGED) {
            if ($command->parentId === null) {
                $folder->setParent(null);
            } else {
                $parent = $this->repository->findById($command->parentId);
                if ($parent === null) {
                    throw new FolderNotFoundException($command->parentId);
                }
                $folder->setParent($parent);
            }
        }

        $this->repository->save($folder);

        return FolderDto::fromEntity($folder);
    }
}
