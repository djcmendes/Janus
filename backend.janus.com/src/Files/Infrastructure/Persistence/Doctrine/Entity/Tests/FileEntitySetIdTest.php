<?php

/**
 * @file FileEntitySetIdTest.php
 *
 * Tests for FileEntity::setId() and FileEntity::getId().
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

#[CoversClass(className: FileEntity::class)]
#[CoversMethod(FileEntity::class, 'setId')]
final class FileEntitySetIdTest extends FileEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetIdStoresUuid(): void
    {
        $uuid = Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001');
        $this->class->setId($uuid);

        $this->assertSame($uuid, $this->class->getId());
    }

    public function testSetIdReturnsStaticInstance(): void
    {
        $result = $this->class->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertSame($this->class, $result);
    }
}
