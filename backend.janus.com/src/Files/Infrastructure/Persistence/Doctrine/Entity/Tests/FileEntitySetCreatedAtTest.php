<?php

/**
 * @file FileEntitySetCreatedAtTest.php
 *
 * Tests for FileEntity::setCreatedAt() and FileEntity::getCreatedAt().
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
#[CoversMethod(FileEntity::class, 'setCreatedAt')]
final class FileEntitySetCreatedAtTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetCreatedAtStoresValue(): void
    {
        $ts = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $this->class->setCreatedAt($ts);

        $this->assertSame($ts, $this->class->getCreatedAt());
    }

    public function testSetCreatedAtReturnsStaticInstance(): void
    {
        $result = $this->class->setCreatedAt(new \DateTimeImmutable());

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetCreatedAtOverwritesPreviousValue(): void
    {
        $first  = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $second = new \DateTimeImmutable('2024-06-01T00:00:00+00:00');

        $this->class->setCreatedAt($first);
        $this->class->setCreatedAt($second);

        $this->assertSame($second, $this->class->getCreatedAt());
    }
}
