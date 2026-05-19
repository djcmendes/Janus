<?php

/**
 * @file FileEntitySetStorageTest.php
 *
 * Tests for FileEntity::setStorage() and FileEntity::getStorage().
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
#[CoversMethod(FileEntity::class, 'setStorage')]
final class FileEntitySetStorageTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetStorageStoresValue(): void
    {
        $this->class->setStorage('s3');

        $this->assertSame('s3', $this->class->getStorage());
    }

    public function testSetStorageReturnsStaticInstance(): void
    {
        $result = $this->class->setStorage('local');

        $this->assertSame($this->class, $result);
    }
}
