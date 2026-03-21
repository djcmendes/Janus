<?php

declare(strict_types=1);

namespace App\Files\Application\Command\Handler;

use App\Files\Application\Command\DeleteFolderCommand;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Domain\Repository\FolderRepositoryInterface;

final class DeleteFolderHandler
{
    public function __construct(private readonly FolderRepositoryInterface $repository) {}

    /** @throws FolderNotFoundException */
    public function handle(DeleteFolderCommand $command): void
    {
        $folder = $this->repository->findById($command->id);

        if ($folder === null) {
            throw new FolderNotFoundException($command->id);
        }

        $this->repository->delete($folder);
    }
}
