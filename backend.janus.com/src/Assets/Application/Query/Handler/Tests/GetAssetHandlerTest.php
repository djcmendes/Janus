<?php

/**
 * @file GetAssetHandlerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * a File entity factory for all GetAssetHandler test cases.
 *
 * Strategy: GetAssetHandler is `final` and cannot be mocked. It is
 * instantiated as a real object. FileRepositoryInterface is an interface
 * and is mocked normally. FileStorageService and AssetTransformService are
 * both `final` with no external I/O dependencies beyond the local filesystem,
 * so they are instantiated as real objects pointed at a temporary directory.
 * A GD-generated JPEG is written to that directory in setUp() so the happy
 * path exercises the full transform pipeline without network or DB access.
 *
 * @package App\Assets\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query\Handler\Tests;

use App\Assets\Application\Query\Handler\GetAssetHandler;
use App\Assets\Domain\Service\AssetTransformService;
use App\Files\Domain\Entity\File;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, fixtures, and factories for all GetAssetHandler
 * test suites.
 */
#[CoversClass(className: GetAssetHandler::class)]
abstract class GetAssetHandlerTest extends TestCase
{
    /** @var string UUID used as the default file identifier in factory methods. */
    protected const string FIXED_FILE_ID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /** @var string Filename of the JPEG written to $tempDir in setUp(). */
    protected const string FIXTURE_FILENAME_DISK = 'test-asset.jpg';

    /**
     * Mock of the domain file repository.
     * @var MockObject&FileRepositoryInterface
     */
    protected MockObject $fileRepository;

    /**
     * Real FileStorageService backed by a temporary directory.
     * @var FileStorageService
     */
    protected FileStorageService $storage;

    /**
     * Real AssetTransformService — no injected dependencies.
     * @var AssetTransformService
     */
    protected AssetTransformService $transformer;

    /**
     * The system under test.
     * @var GetAssetHandler
     */
    protected GetAssetHandler $class;

    /**
     * Reflection of GetAssetHandler for reading private properties.
     * @var ReflectionClass<GetAssetHandler>
     */
    protected ReflectionClass $reflection;

    /**
     * Temporary directory containing the fixture image written by setUp().
     * @var string
     */
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/janus_test_get_asset_' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);

        $img = imagecreatetruecolor(16, 9);
        $col = imagecolorallocate($img, 0, 128, 255);
        imagefilledrectangle($img, 0, 0, 15, 8, $col);
        imagejpeg($img, $this->tempDir . '/' . self::FIXTURE_FILENAME_DISK, 85);
        imagedestroy($img);

        $this->fileRepository = $this->createMock(FileRepositoryInterface::class);
        $this->storage        = new FileStorageService($this->tempDir);
        $this->transformer    = new AssetTransformService();

        $this->class      = new GetAssetHandler($this->fileRepository, $this->storage, $this->transformer);
        $this->reflection = new ReflectionClass(GetAssetHandler::class);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob($this->tempDir . '/*') ?: []);
            rmdir($this->tempDir);
        }

        unset($this->fileRepository, $this->storage, $this->transformer, $this->class, $this->reflection);
    }

    /**
     * Creates a domain File entity pointing at the fixture JPEG in $tempDir.
     *
     * @param string $filenameDisk     Disk filename — must match a file inside $tempDir.
     * @param string $type             MIME type declared on the record.
     * @param string $filenameDownload Original filename shown on download.
     *
     * @return File A fully-hydrated domain entity with deterministic test values.
     */
    protected function makeFile(
        string $filenameDisk     = self::FIXTURE_FILENAME_DISK,
        string $type             = 'image/jpeg',
        string $filenameDownload = 'photo.jpg',
    ): File {
        return File::reconstitute(
            id:               self::FIXED_FILE_ID,
            storage:          'local',
            filenameDisk:     $filenameDisk,
            filenameDownload: $filenameDownload,
            title:            'Test Asset',
            type:             $type,
            filesize:         1024,
            width:            16,
            height:           9,
            uploadedBy:       null,
            folderId:         null,
            createdAt:        new \DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:        null,
        );
    }
}
