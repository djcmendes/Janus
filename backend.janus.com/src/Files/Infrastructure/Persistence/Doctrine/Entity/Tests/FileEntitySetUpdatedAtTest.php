<?php

/**
 * @file FileEntitySetUpdatedAtTest.php
 *
 * Tests for FileEntity::setUpdatedAt() and FileEntity::getUpdatedAt().
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
#[CoversMethod(FileEntity::class, 'setUpdatedAt')]
final class FileEntitySetUpdatedAtTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetUpdatedAtStoresValue(): void
    {
        $ts = new \DateTimeImmutable('2024-06-15T12:00:00+00:00');
        $this->class->setUpdatedAt($ts);

        $this->assertSame($ts, $this->class->getUpdatedAt());
    }

    public function testSetUpdatedAtReturnsStaticInstance(): void
    {
        $result = $this->class->setUpdatedAt(new \DateTimeImmutable());

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetUpdatedAtAcceptsNull(): void
    {
        $this->class->setUpdatedAt(null);

        $this->assertNull($this->class->getUpdatedAt());
    }

    public function testGetUpdatedAtDefaultsToNull(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }
}
