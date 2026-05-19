<?php

/**
 * @file FileMapperToDomainTest.php
 *
 * Tests for FileMapper::toDomain().
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Files\Domain\Entity\File;
use App\Files\Infrastructure\Persistence\Doctrine\Mapper\FileMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: FileMapper::class)]
#[CoversMethod(FileMapper::class, 'toDomain')]
final class FileMapperToDomainTest extends FileMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToDomainReturnsDomainFile(): void
    {
        $this->assertInstanceOf(File::class, $this->class->toDomain($this->makeEntity()));
    }

    public function testToDomainMapsId(): void
    {
        $this->assertSame(self::FIXED_UUID, $this->class->toDomain($this->makeEntity())->getId());
    }

    public function testToDomainMapsStorage(): void
    {
        $this->assertSame('local', $this->class->toDomain($this->makeEntity())->getStorage());
    }

    public function testToDomainMapsFilenameDisk(): void
    {
        $this->assertSame(
            self::FIXED_UUID . '.jpg',
            $this->class->toDomain($this->makeEntity())->getFilenameDisk(),
        );
    }

    public function testToDomainMapsFilenameDownload(): void
    {
        $this->assertSame('photo.jpg', $this->class->toDomain($this->makeEntity())->getFilenameDownload());
    }

    public function testToDomainMapsTitle(): void
    {
        $this->assertSame('Test Photo', $this->class->toDomain($this->makeEntity())->getTitle());
    }

    public function testToDomainMapsType(): void
    {
        $this->assertSame('image/jpeg', $this->class->toDomain($this->makeEntity())->getType());
    }

    public function testToDomainMapsFilesize(): void
    {
        $this->assertSame(204800, $this->class->toDomain($this->makeEntity())->getFilesize());
    }

    public function testToDomainMapsWidth(): void
    {
        $this->assertSame(1920, $this->class->toDomain($this->makeEntity())->getWidth());
    }

    public function testToDomainMapsHeight(): void
    {
        $this->assertSame(1080, $this->class->toDomain($this->makeEntity())->getHeight());
    }

    public function testToDomainMapsUploadedBy(): void
    {
        $this->assertSame(
            'bbbbbbbb-0000-7000-8000-000000000002',
            $this->class->toDomain($this->makeEntity())->getUploadedBy(),
        );
    }

    public function testToDomainMapsFolderIdAsNullWhenEntityFolderIsNull(): void
    {
        $this->assertNull($this->class->toDomain($this->makeEntity())->getFolderId());
    }

    public function testToDomainMapsCreatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $entity = $this->makeEntity()->setCreatedAt($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getCreatedAt());
    }

    public function testToDomainMapsUpdatedAtAsNull(): void
    {
        $this->assertNull($this->class->toDomain($this->makeEntity())->getUpdatedAt());
    }

    public function testToDomainMapsUpdatedAtWhenSet(): void
    {
        $ts     = new \DateTimeImmutable('2024-06-01T00:00:00+00:00');
        $entity = $this->makeEntity()->setUpdatedAt($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToDomainHandlesNullOptionalFields(): void
    {
        $entity = $this->makeEntity()
            ->setTitle(null)
            ->setFilesize(null)
            ->setWidth(null)
            ->setHeight(null)
            ->setUploadedBy(null)
            ->setFolder(null)
            ->setUpdatedAt(null);

        $domain = $this->class->toDomain($entity);

        $this->assertNull($domain->getTitle());
        $this->assertNull($domain->getFilesize());
        $this->assertNull($domain->getWidth());
        $this->assertNull($domain->getHeight());
        $this->assertNull($domain->getUploadedBy());
        $this->assertNull($domain->getFolderId());
        $this->assertNull($domain->getUpdatedAt());
    }

    // Roundtrip ────────────────────────────────────────────────────

    public function testRoundtripPreservesId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getId(), $result->getId());
    }

    public function testRoundtripPreservesStorage(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getStorage(), $result->getStorage());
    }

    public function testRoundtripPreservesFilenameDisk(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getFilenameDisk(), $result->getFilenameDisk());
    }

    public function testRoundtripPreservesType(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getType(), $result->getType());
    }

    public function testRoundtripPreservesUploadedBy(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getUploadedBy(), $result->getUploadedBy());
    }
}
