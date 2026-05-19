<?php

/**
 * @file CommentTest.php
 *
 * Abstract base for all Comment domain entity test suites.
 *
 * @package App\Comments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Entity\Tests;

use App\Comments\Domain\Entity\Comment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for Comment domain entity tests.
 *
 * Strategy: Comment is a final class with no injectable dependencies.
 * Tests instantiate it directly — no mocking is required.
 */
#[CoversClass(className: Comment::class)]
abstract class CommentTest extends TestCase
{
    /**
     * Instance of the class being tested.
     * @var Comment
     */
    protected Comment $class;

    /**
     * Reflection of Comment class.
     * @var ReflectionClass<Comment>
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
        $this->class      = new Comment('posts', '42', 'Hello world', 'aaaaaaaa-0000-7000-8000-000000000001');
        $this->reflection = new ReflectionClass(Comment::class);
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

    /**
     * Creates a fully-populated Comment entity with deterministic test values.
     *
     * @return Comment A hydrated entity ready for assertion.
     */
    protected function makeComment(): Comment
    {
        return new Comment(
            'posts',
            '42',
            'Hello world',
            'aaaaaaaa-0000-7000-8000-000000000001',
        );
    }
}
