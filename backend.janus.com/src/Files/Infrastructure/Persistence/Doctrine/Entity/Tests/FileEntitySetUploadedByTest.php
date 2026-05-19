<?php

/**
 * @file FileEntitySetUploadedByTest.php
 *
 * Tests for FileEntity::setUploadedBy() and FileEntity::getUploadedBy().
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
#[CoversMethod(FileEntity::class, 'setUploadedBy')]
final class FileEntitySetUploadedByTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetUploadedByStoresValue(): void
    {
        $this->class->setUploadedBy('bbbbbbbb-0000-7000-8000-000000000002');

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $this->class->getUploadedBy());
    }

    public function testSetUploadedByReturnsStaticInstance(): void
    {
        $result = $this->class->setUploadedBy('bbbbbbbb-0000-7000-8000-000000000002');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetUploadedByAcceptsNull(): void
    {
        $this->class->setUploadedBy(null);

        $this->assertNull($this->class->getUploadedBy());
    }
}
