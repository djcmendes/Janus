<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

/**
 * Handles physical file storage.
 * Currently supports 'local' disk. S3 support is a future addition.
 */
final class FileStorageService
{
    /** Absolute path to the local storage directory */
    private string $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = rtrim($storagePath, '/');
    }

    /**
     * Moves the uploaded file to storage and returns the stored filename.
     *
     * @throws \RuntimeException on storage failure
     */
    public function store(UploadedFile $file, string $driver = 'local'): string
    {
        if ($driver !== 'local') {
            throw new \RuntimeException(sprintf('Storage driver "%s" is not yet supported.', $driver));
        }

        $extension  = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
        $storedName = Uuid::v7()->toRfc4122() . '.' . $extension;

        $file->move($this->storagePath, $storedName);

        return $storedName;
    }

    /**
     * Deletes a file from storage.
     * Silently ignores missing files to keep delete operations idempotent.
     */
    public function delete(string $filename, string $driver = 'local'): void
    {
        if ($driver !== 'local') {
            // S3 deletion would be handled here
            return;
        }

        $path = $this->storagePath . '/' . $filename;

        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Returns the absolute path to a locally stored file.
     */
    public function getLocalPath(string $filename): string
    {
        return $this->storagePath . '/' . $filename;
    }
}
