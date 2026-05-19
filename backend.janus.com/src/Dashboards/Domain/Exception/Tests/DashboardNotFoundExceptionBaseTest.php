<?php

/**
 * @file DashboardNotFoundExceptionBaseTest.php
 *
 * Constructor and interface compliance tests for DashboardNotFoundException.
 *
 * @package App\Dashboards\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Exception\Tests;

use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that DashboardNotFoundException is a RuntimeException with the correct message format.
 */
#[CoversClass(className: DashboardNotFoundException::class)]
final class DashboardNotFoundExceptionBaseTest extends DashboardNotFoundExceptionTest
{
    /**
     * Test that the SUT extends RuntimeException.
     */
    public function testExtendsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->class);
    }

    /**
     * Test that the message includes the provided UUID.
     */
    public function testMessageContainsId(): void
    {
        $this->assertStringContainsString('test-uuid-001', $this->class->getMessage());
    }

    /**
     * Test that the message matches the expected format.
     */
    public function testMessageMatchesFormat(): void
    {
        $this->assertSame("Dashboard 'test-uuid-001' not found.", $this->class->getMessage());
    }

    /**
     * Test that the message format is correct for a different UUID.
     */
    public function testMessageFormatWithArbitraryId(): void
    {
        $e = new DashboardNotFoundException('some-other-uuid');

        $this->assertSame("Dashboard 'some-other-uuid' not found.", $e->getMessage());
    }
}
