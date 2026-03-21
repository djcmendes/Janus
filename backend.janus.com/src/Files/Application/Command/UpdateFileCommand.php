<?php

declare(strict_types=1);

namespace App\Files\Application\Command;

final class UpdateFileCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string $id,
        public readonly mixed  $title            = self::UNCHANGED,
        public readonly mixed  $filenameDownload = self::UNCHANGED,
        public readonly mixed  $folderId         = self::UNCHANGED,
    ) {}
}
