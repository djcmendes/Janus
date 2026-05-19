<?php

/**
 * @file GetAssetHandler.php
 *
 * Query handler that retrieves a stored file and returns its transformed binary content.
 *
 * @package App\Assets\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query\Handler;

use App\Assets\Application\DTO\TransformedAssetDto;
use App\Assets\Application\Query\GetAssetQuery;
use App\Assets\Domain\Service\AssetTransformService;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;

/**
 * Resolves a file record from the repository, validates its physical existence
 * on disk, applies optional image transformations, and returns the result as a DTO.
 */
final class GetAssetHandler
{
    /** @var string[] Accepted output format identifiers. */
    private const ALLOWED_FORMATS = ['jpg', 'png', 'webp'];

    /** @var string[] Accepted resize fit modes. */
    private const ALLOWED_FITS    = ['contain', 'cover', 'fill'];

    /**
     * @param FileRepositoryInterface $fileRepository  Resolves file metadata by UUID.
     * @param FileStorageService      $storage         Resolves the absolute local path for a stored filename.
     * @param AssetTransformService   $transformer     Applies resize, crop, and format transforms via GD.
     */
    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FileStorageService      $storage,
        private readonly AssetTransformService   $transformer,
    ) {}

    /**
     * Loads the file record, validates the physical source, applies transforms, and returns a DTO.
     *
     * Falls back to 'contain' when $query->fit is not in the allowed list, and derives
     * the output format from the file's MIME type when $query->format is not allowed.
     *
     * @param  GetAssetQuery       $query  The retrieval and transform parameters.
     *
     * @return TransformedAssetDto  The transformed binary content, resolved MIME type, and download filename.
     *
     * @throws FileNotFoundException  When no file record exists for the given ID, or the physical file is missing from disk.
     */
    public function handle(GetAssetQuery $query): TransformedAssetDto
    {
        $file = $this->fileRepository->findById($query->id);

        if ($file === null) {
            throw new FileNotFoundException($query->id);
        }

        $sourcePath = $this->storage->getLocalPath($file->getFilenameDisk());

        if (!is_file($sourcePath)) {
            throw new FileNotFoundException($query->id);
        }

        $format = in_array($query->format, self::ALLOWED_FORMATS, true)
            ? $query->format
            : $this->mimeToFormat($file->getType());

        $fit = in_array($query->fit, self::ALLOWED_FITS, true) ? $query->fit : 'contain';

        $result = $this->transformer->transform(
            $sourcePath,
            $file->getType(),
            $query->width,
            $query->height,
            $fit,
            $format,
        );

        return new TransformedAssetDto(
            $result['content'],
            $result['mimeType'],
            $file->getFilenameDownload(),
        );
    }

    /**
     * Derives a GD-compatible format string from a MIME type string.
     *
     * Returns 'png' for MIME types containing 'png', 'webp' for 'webp', and 'jpg' for all others.
     *
     * @param  string $mime  MIME type declared on the File record (e.g. 'image/jpeg').
     *
     * @return string  One of 'jpg', 'png', or 'webp'.
     */
    private function mimeToFormat(string $mime): string
    {
        return match (true) {
            str_contains($mime, 'png')  => 'png',
            str_contains($mime, 'webp') => 'webp',
            default                     => 'jpg',
        };
    }
}
