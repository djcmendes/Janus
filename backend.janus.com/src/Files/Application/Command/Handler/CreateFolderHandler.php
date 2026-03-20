<?php

declare(strict_types=1);

namespace App\Files\Application\Command\Handler;

use App\Files\Application\Command\CreateFolderCommand;
use App\Files\Application\DTO\FolderDto;
use App\Files\Domain\Entity\Folder;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Domain\Repository\FolderRepositoryInterface;

final class CreateFolderHandler
{
    public function __construct(private readonly FolderRepositoryInterface $repository) {}

    /** @throws FolderNotFoundException */
    public function handle(CreateFolderCommand $command): FolderDto
    {
        $folder = new Folder($command->name);

        if ($command->parentId !== null) {
            $parent = $this->repository->findById($command->parentId);
            if ($parent === null) {
                throw new FolderNotFoundException($command->parentId);
            }
            $folder->setParent($parent);
        }

        $this->repository->save($folder);

        return FolderDto::fromEntity($folder);
    }
}
