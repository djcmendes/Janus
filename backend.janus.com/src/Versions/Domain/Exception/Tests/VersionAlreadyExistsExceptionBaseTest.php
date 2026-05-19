<?php

/**
 * @file VersionAlreadyExistsExceptionBaseTest.php
 *
 * Tests for VersionAlreadyExistsException construction and message format.
 *
 * @package App\Versions\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Exception\Tests;

use App\Versions\Domain\Exception\VersionAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionAlreadyExistsException is a RuntimeException with the expected message.
 */
#[CoversClass(className: VersionAlreadyExistsException::class)]
final class VersionAlreadyExistsExceptionBaseTest extends VersionAlreadyExistsExceptionTest
{
    /**
     * Test that VersionAlreadyExistsException extends RuntimeException.
     */
    public function testExtendsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->class);
    }

    /**
     * Test that the message includes the collection, item, and key.
     */
    public function testMessageContainsAllTripletParts(): void
    {
        $message = $this->class->getMessage();

        $this->assertStringContainsString('articles', $message);
        $this->assertStringContainsString('item-uuid-1', $message);
        $this->assertStringContainsString('main', $message);
    }

    /**
     * Test that the message follows the expected format.
     */
    public function testMessageFormat(): void
    {
        $this->assertSame(
            'A version with key "main" already exists for item "item-uuid-1" in collection "articles".',
            $this->class->getMessage(),
        );
    }
}
