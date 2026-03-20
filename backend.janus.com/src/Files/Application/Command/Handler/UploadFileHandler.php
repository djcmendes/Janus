<?php

declare(strict_types=1);

namespace App\Files\Application\Command\Handler;

use App\Files\Application\Command\UploadFileCommand;
use App\Files\Application\DTO\FileDto;
use App\Files\Domain\Entity\File;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Domain\Repository\FolderRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;

final class UploadFileHandler
{
    public function __construct(
        private readonly FileRepositoryInterface   $fileRepository,
        private readonly FolderRepositoryInterface $folderRepository,
        private readonly FileStorageService        $storage,
    ) {}

    /** @throws FolderNotFoundException */
    public function handle(UploadFileCommand $command): FileDto
    {
        $folder = null;
        if ($command->folderId !== null) {
            $folder = $this->folderRepository->findById($command->folderId);
            if ($folder === null) {
                throw new FolderNotFoundException($command->folderId);
            }
        }

        $uploaded = $command->file;
        $mimeType = $uploaded->getMimeType() ?? 'application/octet-stream';
        $originalName = $uploaded->getClientOriginalName();

        [$width, $height] = $this->resolveImageDimensions($uploaded->getPathname(), $mimeType);

        $storedName = $this->storage->store($uploaded);

        $file = new File(
            filenameDisk:     $storedName,
            filenameDownload: $originalName,
            type:             $mimeType,
            filesize:         $uploaded->getSize() ?: null,
            width:            $width,
            height:           $height,
        );

        $file->setTitle($command->title);
        $file->setFolder($folder);
        $file->setUploadedBy($command->uploadedBy);

        $this->fileRepository->save($file);

        return FileDto::fromEntity($file);
    }

    private function resolveImageDimensions(string $path, string $mimeType): array
    {
        if (!str_starts_with($mimeType, 'image/') || !function_exists('getimagesize')) {
            return [null, null];
        }

        $info = @getimagesize($path);
        return $info !== false ? [$info[0], $info[1]] : [null, null];
    }
}
