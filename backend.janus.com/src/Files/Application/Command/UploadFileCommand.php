<?php

declare(strict_types=1);

namespace App\Files\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadFileCommand
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly ?string      $title    = null,
        public readonly ?string      $folderId = null,
        public readonly ?string      $uploadedBy = null,
    ) {}
}
