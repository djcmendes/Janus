<?php

/**
 * @file FileEntitySetWidthTest.php
 *
 * Tests for FileEntity::setWidth() and FileEntity::getWidth().
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
#[CoversMethod(FileEntity::class, 'setWidth')]
final class FileEntitySetWidthTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetWidthStoresValue(): void
    {
        $this->class->setWidth(1920);

        $this->assertSame(1920, $this->class->getWidth());
    }

    public function testSetWidthReturnsStaticInstance(): void
    {
        $result = $this->class->setWidth(800);

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetWidthAcceptsNull(): void
    {
        $this->class->setWidth(null);

        $this->assertNull($this->class->getWidth());
    }
}
