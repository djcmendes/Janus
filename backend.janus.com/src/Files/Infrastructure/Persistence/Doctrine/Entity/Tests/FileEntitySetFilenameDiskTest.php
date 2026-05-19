<?php

/**
 * @file FileEntitySetFilenameDiskTest.php
 *
 * Tests for FileEntity::setFilenameDisk() and FileEntity::getFilenameDisk().
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
#[CoversMethod(FileEntity::class, 'setFilenameDisk')]
final class FileEntitySetFilenameDiskTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetFilenameDiskStoresValue(): void
    {
        $this->class->setFilenameDisk('01957abc-def0-7000-8000-000000000001.png');

        $this->assertSame('01957abc-def0-7000-8000-000000000001.png', $this->class->getFilenameDisk());
    }

    public function testSetFilenameDiskReturnsStaticInstance(): void
    {
        $result = $this->class->setFilenameDisk('01957abc-def0-7000-8000-000000000001.png');

        $this->assertSame($this->class, $result);
    }
}
