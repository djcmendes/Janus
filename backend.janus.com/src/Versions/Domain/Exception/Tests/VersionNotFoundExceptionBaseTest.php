<?php

/**
 * @file VersionNotFoundExceptionBaseTest.php
 *
 * Tests for VersionNotFoundException construction and message format.
 *
 * @package App\Versions\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Exception\Tests;

use App\Versions\Domain\Exception\VersionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionNotFoundException is a RuntimeException with the expected message format.
 */
#[CoversClass(className: VersionNotFoundException::class)]
final class VersionNotFoundExceptionBaseTest extends VersionNotFoundExceptionTest
{
    /**
     * Test that VersionNotFoundException extends RuntimeException.
     */
    public function testExtendsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->class);
    }

    /**
     * Test that the exception message includes the provided UUID.
     */
    public function testMessageContainsUuid(): void
    {
        $this->assertStringContainsString(self::MISSING_UUID, $this->class->getMessage());
    }

    /**
     * Test that the exception message follows the expected format.
     */
    public function testMessageFormat(): void
    {
        $this->assertSame(
            sprintf('Version "%s" not found.', self::MISSING_UUID),
            $this->class->getMessage(),
        );
    }
}
