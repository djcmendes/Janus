<?php

/**
 * @file FileEntitySetFilenameDownloadTest.php
 *
 * Tests for FileEntity::setFilenameDownload() and FileEntity::getFilenameDownload().
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: FileEntity::class)]
#[CoversMethod(FileEntity::class, 'setFilenameDownload')]
final class FileEntitySetFilenameDownloadTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetFilenameDownloadStoresValue(): void
    {
        $this->class->setFilenameDownload('avatar.png');

        $this->assertSame('avatar.png', $this->class->getFilenameDownload());
    }

    public function testSetFilenameDownloadReturnsStaticInstance(): void
    {
        $result = $this->class->setFilenameDownload('avatar.png');

        $this->assertSame($this->class, $result);
    }
}
