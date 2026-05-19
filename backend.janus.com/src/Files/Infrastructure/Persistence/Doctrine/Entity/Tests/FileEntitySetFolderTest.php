<?php

/**
 * @file FileEntitySetFolderTest.php
 *
 * Tests for FileEntity::setFolder() and FileEntity::getFolder().
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Files\Domain\Entity\Folder;
use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: FileEntity::class)]
#[CoversMethod(FileEntity::class, 'setFolder')]
final class FileEntitySetFolderTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetFolderStoresFolderInstance(): void
    {
        $folder = new Folder('uploads');
        $this->class->setFolder($folder);

        $this->assertSame($folder, $this->class->getFolder());
    }

    public function testSetFolderReturnsStaticInstance(): void
    {
        $result = $this->class->setFolder(new Folder('uploads'));

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetFolderAcceptsNull(): void
    {
        $this->class->setFolder(null);

        $this->assertNull($this->class->getFolder());
    }

    public function testGetFolderDefaultsToNull(): void
    {
        $this->assertNull($this->class->getFolder());
    }
}
