<?php

/**
 * @file FileEntitySetTypeTest.php
 *
 * Tests for FileEntity::setType() and FileEntity::getType().
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
#[CoversMethod(FileEntity::class, 'setType')]
final class FileEntitySetTypeTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetTypeStoresValue(): void
    {
        $this->class->setType('image/png');

        $this->assertSame('image/png', $this->class->getType());
    }

    public function testSetTypeReturnsStaticInstance(): void
    {
        $result = $this->class->setType('image/jpeg');

        $this->assertSame($this->class, $result);
    }
}
