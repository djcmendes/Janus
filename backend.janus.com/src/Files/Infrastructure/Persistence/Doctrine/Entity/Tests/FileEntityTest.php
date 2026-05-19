<?php

/**
 * @file FileEntityTest.php
 *
 * Abstract base for all FileEntity test suites.
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for FileEntity tests.
 *
 * Strategy: FileEntity has no injectable dependencies. Tests instantiate it
 * directly — no mocking is required. The class is non-final (required for
 * Doctrine proxy generation), so a real instance is used as the SUT.
 */
#[CoversClass(className: FileEntity::class)]
abstract class FileEntityTest extends TestCase
{
    /** @var FileEntity The entity instance under test. */
    protected FileEntity $class;

    /** @var ReflectionClass<FileEntity> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new FileEntity();
        $this->reflection = new ReflectionClass(FileEntity::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated FileEntity with deterministic test values.
     *
     * @return FileEntity A hydrated entity ready for assertion.
     */
    protected function makeEntity(): FileEntity
    {
        return (new FileEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setStorage('local')
            ->setFilenameDisk('aaaaaaaa-0000-7000-8000-000000000001.jpg')
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
}
