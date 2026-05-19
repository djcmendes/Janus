<?php

/**
 * @file FileEntitySetTitleTest.php
 *
 * Tests for FileEntity::setTitle() and FileEntity::getTitle().
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
#[CoversMethod(FileEntity::class, 'setTitle')]
final class FileEntitySetTitleTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetTitleStoresValue(): void
    {
        $this->class->setTitle('Hero Banner');

        $this->assertSame('Hero Banner', $this->class->getTitle());
    }

    public function testSetTitleReturnsStaticInstance(): void
    {
        $result = $this->class->setTitle('Hero Banner');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetTitleAcceptsNull(): void
    {
        $this->class->setTitle(null);

        $this->assertNull($this->class->getTitle());
    }
}
