<?php

/**
 * @file VersionEntitySetIdTest.php
 *
 * Tests for VersionEntity::setId().
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

/**
 * Verifies that setId() stores the Uuid and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setId')]
final class VersionEntitySetIdTest extends VersionEntityTest
{
    public function testSetIdStoresUuid(): void
    {
        $uuid = Uuid::fromString('cccccccc-0000-7000-8000-000000000003');
        $this->class->setId($uuid);

        $this->assertSame($uuid, $this->class->getId());
    }

    public function testSetIdReturnsStatic(): void
    {
        $result = $this->class->setId(Uuid::fromString('cccccccc-0000-7000-8000-000000000003'));

        $this->assertSame($this->class, $result);
    }
}
