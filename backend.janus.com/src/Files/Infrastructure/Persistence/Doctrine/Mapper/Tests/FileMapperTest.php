<?php

/**
 * @file FileMapperTest.php
 *
 * Abstract base for all FileMapper test suites.
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Files\Domain\Entity\File;
use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use App\Files\Infrastructure\Persistence\Doctrine\Mapper\FileMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for FileMapper tests.
 *
 * Strategy: FileMapper, File, and FileEntity are all instantiated as real
 * objects. All three classes are pure with no injectable dependencies, so
 * no mocking is required.
 */
#[CoversClass(className: FileMapper::class)]
abstract class FileMapperTest extends TestCase
{
    /** @var string */
    protected const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    protected FileMapper $class;

    /** @var ReflectionClass<FileMapper> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new FileMapper();
        $this->reflection = new ReflectionClass(FileMapper::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated FileEntity with deterministic test values.
     *
     * @return FileEntity A hydrated Doctrine model ready for toDomain() tests.
     */
    protected function makeEntity(): FileEntity
    {
        return (new FileEntity())
            ->setId(Uuid::fromString(self::FIXED_UUID))
            ->setStorage('local')
            ->setFilenameDisk(self::FIXED_UUID . '.jpg')
            ->setFilenameDownload('photo.jpg')
            ->setTitle('Test Photo')
            ->setType('image/jpeg')
            ->setFilesize(204800)
            ->setWidth(1920)
            ->setHeight(1080)
            ->setUploadedBy('bbbbbbbb-0000-7000-8000-000000000002')
            ->setFolder(null)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }

    /**
     * Creates a fully-populated domain File with deterministic test values.
     *
     * @return File A hydrated domain entity ready for toPersistence() tests.
     */
    protected function makeDomain(): File
    {
        return File::reconstitute(
            id:               self::FIXED_UUID,
            storage:          'local',
            filenameDisk:     self::FIXED_UUID . '.jpg',
            filenameDownload: 'photo.jpg',
            title:            'Test Photo',
            type:             'image/jpeg',
            filesize:         204800,
            width:            1920,
            height:           1080,
            uploadedBy:       'bbbbbbbb-0000-7000-8000-000000000002',
            folderId:         null,
            createdAt:        new \DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:        null,
        );
    }
}
