<?php

/**
 * @file CommentNotFoundExceptionBaseTest.php
 *
 * Constructor and interface compliance tests for CommentNotFoundException.
 *
 * @package App\Comments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Exception\Tests;

use App\Comments\Domain\Exception\CommentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that CommentNotFoundException is correctly constructed
 * and extends the expected base exception class.
 */
#[CoversClass(className: CommentNotFoundException::class)]
final class CommentNotFoundExceptionBaseTest extends CommentNotFoundExceptionTest
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
     * Test that the exception message includes the provided id.
     */
    public function testMessageIncludesId(): void
    {
        $this->assertStringContainsString(
            'aaaaaaaa-0000-7000-8000-000000000001',
            $this->class->getMessage(),
        );
    }

    /**
     * Test that the exception message indicates the comment was not found.
     */
    public function testMessageIndicatesNotFound(): void
    {
        $this->assertStringContainsString('not found', strtolower($this->class->getMessage()));
    }
}
