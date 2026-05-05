<?php

/**
 * @file ActivityNotFoundExceptionBaseTest.php
 *
 * Constructor and interface compliance tests for ActivityNotFoundException.
 *
 * @package App\Activity\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Exception\Tests;

use App\Activity\Domain\Exception\ActivityNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ActivityNotFoundException::class)]
final class ActivityNotFoundExceptionBaseTest extends ActivityNotFoundExceptionTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->class);
    }

    public function testExceptionMessageContainsLookupId(): void
    {
        $this->assertStringContainsString(self::LOOKUP_UUID, $this->class->getMessage());
    }

    public function testExceptionMessageMatchesExpectedFormat(): void
    {
        $this->assertSame(
            sprintf('Activity "%s" not found.', self::LOOKUP_UUID),
            $this->class->getMessage(),
        );
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testMessageFormatHoldsForAnyUuid(): void
    {
        $uuid      = 'cccccccc-0000-7000-8000-000000000099';
        $exception = new ActivityNotFoundException($uuid);

        $this->assertSame(sprintf('Activity "%s" not found.', $uuid), $exception->getMessage());
    }
}
