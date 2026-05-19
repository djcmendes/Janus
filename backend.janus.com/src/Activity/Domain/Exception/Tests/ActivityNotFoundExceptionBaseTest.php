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
use RuntimeException;

/**
 * Verifies that ActivityNotFoundException carries the correct message format.
 */
#[CoversClass(className:  ActivityNotFoundException::class)]
final class ActivityNotFoundExceptionBaseTest extends ActivityNotFoundExceptionTest
{
    /**
     * Test that ActivityNotFoundException extends RuntimeException.
     */
    public function testIsInstanceOfRuntimeException(): void
    {
        $this->assertInstanceOf(expected: RuntimeException::class, actual: $this->class);
    }

    /**
     * Test that the exception message contains the activity UUID.
     */
    public function testExceptionMessageContainsLookupId(): void
    {
        $this->assertStringContainsString(needle: self::LOOKUP_UUID, haystack: $this->class->getMessage());
    }

    /**
     * Test that the exception message matches the expected format exactly.
     */
    public function testExceptionMessageMatchesExpectedFormat(): void
    {
        $this->assertSame(
            expected: sprintf(format: 'Activity "%s" not found.', values: self::LOOKUP_UUID),
            actual:   $this->class->getMessage(),
        );
    }

    /**
     * Test that the message format is consistent for any supplied UUID.
     */
    public function testMessageFormatHoldsForAnyUuid(): void
    {
        $uuid      = 'cccccccc-0000-7000-8000-000000000099';
        $exception = new ActivityNotFoundException(id: $uuid);

        $this->assertSame(
            expected: sprintf(format: 'Activity "%s" not found.', values: $uuid),
            actual:   $exception->getMessage()
        );
    }
}
