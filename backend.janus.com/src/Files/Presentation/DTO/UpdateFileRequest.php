<?php

declare(strict_types=1);

namespace App\Files\Presentation\DTO;

use App\Files\Application\Command\UpdateFileCommand;

final class UpdateFileRequest
{
    public function __construct(
        public readonly mixed $title,
        public readonly mixed $filenameDownload,
        public readonly mixed $folderId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title:            array_key_exists('title', $data)             ? $data['title']             : UpdateFileCommand::UNCHANGED,
            filenameDownload: array_key_exists('filename_download', $data) ? $data['filename_download'] : UpdateFileCommand::UNCHANGED,
            folderId:         array_key_exists('folder', $data)            ? $data['folder']            : UpdateFileCommand::UNCHANGED,
        );
    }
}
