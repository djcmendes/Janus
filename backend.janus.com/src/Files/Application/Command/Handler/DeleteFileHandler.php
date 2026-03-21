<?php

declare(strict_types=1);

namespace App\Files\Application\Command\Handler;

use App\Files\Application\Command\DeleteFileCommand;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;

final class DeleteFileHandler
{
    public function __construct(
        private readonly FileRepositoryInterface $repository,
        private readonly FileStorageService      $storage,
    ) {}

    /** @throws FileNotFoundException */
    public function handle(DeleteFileCommand $command): void
    {
        $file = $this->repository->findById($command->id);

        if ($file === null) {
            throw new FileNotFoundException($command->id);
        }

        $this->repository->delete($file);
        $this->storage->delete($file->getFilenameDisk(), $file->getStorage());
    }
}
