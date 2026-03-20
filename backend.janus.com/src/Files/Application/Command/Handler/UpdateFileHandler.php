<?php

declare(strict_types=1);

namespace App\Files\Application\Command\Handler;

use App\Files\Application\Command\UpdateFileCommand;
use App\Files\Application\DTO\FileDto;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Domain\Repository\FolderRepositoryInterface;

final class UpdateFileHandler
{
    public function __construct(
        private readonly FileRepositoryInterface   $fileRepository,
        private readonly FolderRepositoryInterface $folderRepository,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws FolderNotFoundException
     */
    public function handle(UpdateFileCommand $command): FileDto
    {
        $file = $this->fileRepository->findById($command->id);

        if ($file === null) {
            throw new FileNotFoundException($command->id);
        }

        if ($command->title !== UpdateFileCommand::UNCHANGED) {
            $file->setTitle($command->title);
        }

        if ($command->filenameDownload !== UpdateFileCommand::UNCHANGED) {
            if ($command->filenameDownload !== null) {
                $file->setFilenameDownload($command->filenameDownload);
            }
        }

        if ($command->folderId !== UpdateFileCommand::UNCHANGED) {
            if ($command->folderId === null) {
                $file->setFolder(null);
            } else {
                $folder = $this->folderRepository->findById($command->folderId);
                if ($folder === null) {
                    throw new FolderNotFoundException($command->folderId);
                }
                $file->setFolder($folder);
            }
        }

        $this->fileRepository->save($file);

        return FileDto::fromEntity($file);
    }
}
