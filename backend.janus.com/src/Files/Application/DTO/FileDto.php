<?php

declare(strict_types=1);

namespace App\Files\Application\DTO;

use App\Files\Domain\Entity\File;

final class FileDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $storage,
        public readonly string  $filenameDisk,
        public readonly string  $filenameDownload,
        public readonly ?string $title,
        public readonly string  $type,
        public readonly ?int    $filesize,
        public readonly ?int    $width,
        public readonly ?int    $height,
        public readonly ?string $uploadedBy,
        public readonly ?string $folderId,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(File $f): self
    {
        return new self(
            id:               (string) $f->getId(),
            storage:          $f->getStorage(),
            filenameDisk:     $f->getFilenameDisk(),
            filenameDownload: $f->getFilenameDownload(),
            title:            $f->getTitle(),
            type:             $f->getType(),
            filesize:         $f->getFilesize(),
            width:            $f->getWidth(),
            height:           $f->getHeight(),
            uploadedBy:       $f->getUploadedBy(),
            folderId:         $f->getFolder() ? (string) $f->getFolder()->getId() : null,
            createdAt:        $f->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:        $f->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'storage'           => $this->storage,
            'filename_disk'     => $this->filenameDisk,
            'filename_download' => $this->filenameDownload,
            'title'             => $this->title,
            'type'              => $this->type,
            'filesize'          => $this->filesize,
            'width'             => $this->width,
            'height'            => $this->height,
            'uploaded_by'       => $this->uploadedBy,
            'folder'            => $this->folderId,
            'created_at'        => $this->createdAt,
            'updated_at'        => $this->updatedAt,
        ];
    }
}
