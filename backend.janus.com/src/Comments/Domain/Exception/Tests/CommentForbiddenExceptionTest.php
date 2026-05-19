<?php

/**
 * @file CommentForbiddenExceptionTest.php
 *
 * Abstract base for all CommentForbiddenException test suites.
 *
 * @package App\Comments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Exception\Tests;

use App\Comments\Domain\Exception\CommentForbiddenException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for CommentForbiddenException tests.
 *
 * Strategy: CommentForbiddenException is a final class with no injectable
 * dependencies. Tests instantiate it directly.
 */
#[CoversClass(className: CommentForbiddenException::class)]
abstract class CommentForbiddenExceptionTest extends TestCase
{
    /**
     * Instance of the class being tested.
     * @var CommentForbiddenException
     */
    protected CommentForbiddenException $class;

    /**
     * Reflection of CommentForbiddenException.
     * @var ReflectionClass<CommentForbiddenException>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->class      = new CommentForbiddenException();
        $this->reflection = new ReflectionClass(CommentForbiddenException::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset(
            $this->class,
            $this->reflection
        );
    }
}
