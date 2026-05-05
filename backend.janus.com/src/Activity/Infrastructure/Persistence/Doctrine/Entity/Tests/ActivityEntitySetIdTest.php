<?php

/**
 * @file ActivityEntitySetIdTest.php
 *
 * Tests for ActivityEntity::setId() and ActivityEntity::getId().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ActivityEntity::class)]
#[CoversMethod(ActivityEntity::class, 'setId')]
final class ActivityEntitySetIdTest extends ActivityEntityTest
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
