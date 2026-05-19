<?php

/**
 * @file FileEntitySetHeightTest.php
 *
 * Tests for FileEntity::setHeight() and FileEntity::getHeight().
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
#[CoversMethod(FileEntity::class, 'setHeight')]
final class FileEntitySetHeightTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetHeightStoresValue(): void
    {
        $this->class->setHeight(1080);

        $this->assertSame(1080, $this->class->getHeight());
    }

    public function testSetHeightReturnsStaticInstance(): void
    {
        $result = $this->class->setHeight(600);

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetHeightAcceptsNull(): void
    {
        $this->class->setHeight(null);

        $this->assertNull($this->class->getHeight());
    }
}
