<?php

/**
 * @file ActivityEntitySetIdTest.php
 *
 * Tests for ActivityEntity::setId() and ActivityEntity::id { get; set }.
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

/**
 * Test class for ActivityEntity::setId() — storing and retrieving the collection.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setId')]
final class ActivityEntitySetIdTest extends ActivityEntityTest
{
    /**
     * Test that setId() stores the values.
     */
    public function testSetIdStoresUuid(): void
    {
        $uuid = Uuid::fromString(uuid: 'aaaaaaaa-0000-7000-8000-000000000001');
        $this->class->setId(id: $uuid);

        $this->assertSame(expected: $uuid, actual: $this->class->id);
    }

    /**
     * Test that setId() returns static instance
     */
    public function testSetIdReturnsStaticInstance(): void
    {
        $result = $this->class->setId(id: Uuid::fromString(uuid: 'aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertSame(expected: $this->class, actual: $result);
    }
}
