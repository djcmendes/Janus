<?php

/**
 * @file ActivityEntitySetUserAgentTest.php
 *
 * Tests for ActivityEntity::setUserAgent() and ActivityEntity::getUserAgent().
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
#[CoversMethod(ActivityEntity::class, 'setUserAgent')]
final class ActivityEntitySetUserAgentTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetUserAgentStoresValue(): void
    {
        $this->class->setUserAgent('Mozilla/5.0');

        $this->assertSame('Mozilla/5.0', $this->class->getUserAgent());
    }

    public function testSetUserAgentReturnsStaticInstance(): void
    {
        $result = $this->class->setUserAgent('Mozilla/5.0');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetUserAgentAcceptsNull(): void
    {
        $this->class->setUserAgent(null);

        $this->assertNull($this->class->getUserAgent());
    }
}
