<?php

/**
 * @file FileMapperToPersistenceTest.php
 *
 * Tests for FileMapper::toPersistence().
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
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

#[CoversClass(className: FileMapper::class)]
#[CoversMethod(FileMapper::class, 'toPersistence')]
final class FileMapperToPersistenceTest extends FileMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToPersistenceReturnsFileEntity(): void
    {
        $this->assertInstanceOf(FileEntity::class, $this->class->toPersistence($this->makeDomain()));
    }

    public function testToPersistenceMapsIdAsUuid(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);

        $this->assertInstanceOf(Uuid::class, $entity->getId());
        $this->assertSame($domain->getId(), (string) $entity->getId());
    }

    public function testToPersistenceMapsStorage(): void
    {
        $this->assertSame('local', $this->class->toPersistence($this->makeDomain())->getStorage());
    }

    public function testToPersistenceMapsFilenameDisk(): void
    {
        $this->assertSame(
            self::FIXED_UUID . '.jpg',
            $this->class->toPersistence($this->makeDomain())->getFilenameDisk(),
        );
    }

    public function testToPersistenceMapsFilenameDownload(): void
    {
        $this->assertSame('photo.jpg', $this->class->toPersistence($this->makeDomain())->getFilenameDownload());
    }

    public function testToPersistenceMapsTitle(): void
    {
        $this->assertSame('Test Photo', $this->class->toPersistence($this->makeDomain())->getTitle());
    }

    public function testToPersistenceMapsType(): void
    {
        $this->assertSame('image/jpeg', $this->class->toPersistence($this->makeDomain())->getType());
    }

    public function testToPersistenceMapsFilesize(): void
    {
        $this->assertSame(204800, $this->class->toPersistence($this->makeDomain())->getFilesize());
    }

    public function testToPersistenceMapsWidth(): void
    {
        $this->assertSame(1920, $this->class->toPersistence($this->makeDomain())->getWidth());
    }

    public function testToPersistenceMapsHeight(): void
    {
        $this->assertSame(1080, $this->class->toPersistence($this->makeDomain())->getHeight());
    }

    public function testToPersistenceMapsUploadedBy(): void
    {
        $this->assertSame(
            'bbbbbbbb-0000-7000-8000-000000000002',
            $this->class->toPersistence($this->makeDomain())->getUploadedBy(),
        );
    }

    public function testToPersistenceLeavesFolderNullByDesign(): void
    {
        // Folder is intentionally not set by the mapper — the FileRepository
        // resolves and assigns the Folder entity via the entity manager.
        $this->assertNull($this->class->toPersistence($this->makeDomain())->getFolder());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToPersistenceMapsNullOptionalFields(): void
    {
        $domain = File::reconstitute(
            id:               self::FIXED_UUID,
            storage:          'local',
            filenameDisk:     self::FIXED_UUID . '.jpg',
            filenameDownload: 'photo.jpg',
            title:            null,
            type:             'image/jpeg',
            filesize:         null,
            width:            null,
            height:           null,
            uploadedBy:       null,
            folderId:         null,
            createdAt:        new \DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:        null,
        );

        $entity = $this->class->toPersistence($domain);

        $this->assertNull($entity->getTitle());
        $this->assertNull($entity->getFilesize());
        $this->assertNull($entity->getWidth());
        $this->assertNull($entity->getHeight());
        $this->assertNull($entity->getUploadedBy());
        $this->assertNull($entity->getFolder());
        $this->assertNull($entity->getUpdatedAt());
    }
}
