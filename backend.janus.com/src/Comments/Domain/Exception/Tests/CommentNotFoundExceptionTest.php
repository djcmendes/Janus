<?php

/**
 * @file CommentNotFoundExceptionTest.php
 *
 * Abstract base for all CommentNotFoundException test suites.
 *
 * @package App\Comments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Exception\Tests;

use App\Comments\Domain\Exception\CommentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for CommentNotFoundException tests.
 *
 * Strategy: CommentNotFoundException is a final class with no injectable
 * dependencies. Tests instantiate it directly.
 */
#[CoversClass(className: CommentNotFoundException::class)]
abstract class CommentNotFoundExceptionTest extends TestCase
{
    /**
     * Instance of the class being tested.
     * @var CommentNotFoundException
     */
    protected CommentNotFoundException $class;

    /**
     * Reflection of CommentNotFoundException.
     * @var ReflectionClass<CommentNotFoundException>
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
        $this->class      = new CommentNotFoundException('aaaaaaaa-0000-7000-8000-000000000001');
        $this->reflection = new ReflectionClass(CommentNotFoundException::class);
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
