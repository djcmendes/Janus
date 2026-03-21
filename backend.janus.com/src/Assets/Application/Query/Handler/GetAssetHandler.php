<?php

declare(strict_types=1);

namespace App\Assets\Application\Query\Handler;

use App\Assets\Application\DTO\TransformedAssetDto;
use App\Assets\Application\Query\GetAssetQuery;
use App\Assets\Domain\Service\AssetTransformService;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;

final class GetAssetHandler
{
    private const ALLOWED_FORMATS = ['jpg', 'png', 'webp'];
    private const ALLOWED_FITS    = ['contain', 'cover', 'fill'];

    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FileStorageService      $storage,
        private readonly AssetTransformService   $transformer,
    ) {}

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

    private function mimeToFormat(string $mime): string
    {
        return match (true) {
            str_contains($mime, 'png')  => 'png',
            str_contains($mime, 'webp') => 'webp',
            default                     => 'jpg',
        };
    }
}
