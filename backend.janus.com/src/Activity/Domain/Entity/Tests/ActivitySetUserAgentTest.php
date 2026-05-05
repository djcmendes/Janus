<?php

/**
 * @file ActivitySetUserAgentTest.php
 *
 * Tests for Activity::setUserAgent().
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
#[CoversMethod(Activity::class, 'setUserAgent')]
final class ActivitySetUserAgentTest extends ActivityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetUserAgentStoresValue(): void
    {
        $this->class->setUserAgent('PHPUnit/10');

        $this->assertSame('PHPUnit/10', $this->class->getUserAgent());
    }

    public function testSetUserAgentReturnsStaticInstance(): void
    {
        $result = $this->class->setUserAgent('PHPUnit/10');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetUserAgentAcceptsNull(): void
    {
        $this->class->setUserAgent(null);

        $this->assertNull($this->class->getUserAgent());
    }
}
