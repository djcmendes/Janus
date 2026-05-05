<?php

/**
 * @file ActivityEntitySetUserIdTest.php
 *
 * Tests for ActivityEntity::setUserId() and ActivityEntity::getUserId().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ActivityEntity::class)]
#[CoversMethod(ActivityEntity::class, 'setUserId')]
final class ActivityEntitySetUserIdTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetUserIdStoresValue(): void
    {
        $this->class->setUserId('user-uuid');

        $this->assertSame('user-uuid', $this->class->getUserId());
    }

    public function testSetUserIdReturnsStaticInstance(): void
    {
        $result = $this->class->setUserId('user-uuid');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetUserIdAcceptsNull(): void
    {
        $this->class->setUserId(null);

        $this->assertNull($this->class->getUserId());
    }
}
