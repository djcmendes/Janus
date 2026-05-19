<?php

/**
 * @file FileEntitySetFilesizeTest.php
 *
 * Tests for FileEntity::setFilesize() and FileEntity::getFilesize().
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
#[CoversMethod(FileEntity::class, 'setFilesize')]
final class FileEntitySetFilesizeTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetFilesizeStoresValue(): void
    {
        $this->class->setFilesize(102400);

        $this->assertSame(102400, $this->class->getFilesize());
    }

    public function testSetFilesizeReturnsStaticInstance(): void
    {
        $result = $this->class->setFilesize(1024);

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetFilesizeAcceptsNull(): void
    {
        $this->class->setFilesize(null);

        $this->assertNull($this->class->getFilesize());
    }
}
