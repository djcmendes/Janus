<?php

/**
 * @file CollectionMetaEntitySetIdTest.php
 *
 * Tests for CollectionMetaEntity::setId().
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionMetaEntity::class)]
#[CoversMethod(CollectionMetaEntity::class, 'setId')]
final class CollectionMetaEntitySetIdTest extends CollectionMetaEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetIdPersistsUuid(): void
    {
        $uuid = Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001');
        $this->class->setId($uuid);

        $this->assertSame($uuid, $this->class->getId());
    }

    public function testSetIdReturnsStatic(): void
    {
        $result = $this->class->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertSame($this->class, $result);
    }
}
