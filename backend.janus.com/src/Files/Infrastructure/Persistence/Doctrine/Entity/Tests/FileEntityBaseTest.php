<?php

/**
 * @file FileEntityBaseTest.php
 *
 * Constructor and interface compliance tests for FileEntity.
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FileEntity::class)]
final class FileEntityBaseTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstantiableWithNoArguments(): void
    {
        $this->assertInstanceOf(FileEntity::class, $this->class);
    }

    public function testGetIdReturnsNullBeforeSetId(): void
    {
        $this->assertNull($this->class->getId());
    }

    public function testFluentSetterChainPopulatesAllFields(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame('local', $entity->getStorage());
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001.jpg', $entity->getFilenameDisk());
        $this->assertSame('photo.jpg', $entity->getFilenameDownload());
        $this->assertSame('Test Photo', $entity->getTitle());
        $this->assertSame('image/jpeg', $entity->getType());
        $this->assertSame(204800, $entity->getFilesize());
        $this->assertSame(1920, $entity->getWidth());
        $this->assertSame(1080, $entity->getHeight());
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $entity->getUploadedBy());
        $this->assertNull($entity->getFolder());
        $this->assertNull($entity->getUpdatedAt());
    }
}
