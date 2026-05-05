<?php

/**
 * @file ActivitySetUserIdTest.php
 *
 * Tests for Activity::setUserId().
 *
 * @package App\Activity\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity\Tests;

use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(Activity::class)]
#[CoversMethod(Activity::class, 'setUserId')]
final class ActivitySetUserIdTest extends ActivityTest
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
