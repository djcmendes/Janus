<?php

/**
 * @file CommentForbiddenExceptionBaseTest.php
 *
 * Constructor and interface compliance tests for CommentForbiddenException.
 *
 * @package App\Comments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Exception\Tests;

use App\Comments\Domain\Exception\CommentForbiddenException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that CommentForbiddenException is correctly constructed
 * and extends the expected base exception class.
 */
#[CoversClass(CommentForbiddenException::class)]
final class CommentForbiddenExceptionBaseTest extends CommentForbiddenExceptionTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the exception is an instance of RuntimeException.
     */
    public function testIsInstanceOfRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->class);
    }

    /**
     * Test that the exception message indicates a permission denial.
     */
    public function testMessageIndicatesPermissionDenied(): void
    {
        $this->assertStringContainsString('permission', strtolower($this->class->getMessage()));
    }
}
