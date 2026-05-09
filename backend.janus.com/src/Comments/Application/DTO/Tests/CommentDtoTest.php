<?php

/**
 * @file CommentDtoTest.php
 *
 * Abstract base for all CommentDto test suites.
 *
 * @package App\Comments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\DTO\Tests;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Domain\Entity\Comment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for CommentDto tests.
 *
 * Strategy: CommentDto and Comment are final classes with no injectable
 * dependencies. Tests instantiate them directly — no mocking is required.
 */
#[CoversClass(CommentDto::class)]
abstract class CommentDtoTest extends TestCase
{
    /**
     * Instance of the class being tested.
     * @var CommentDto
     */
    protected CommentDto $class;

    /**
     * Reflection of CommentDto.
     * @var ReflectionClass<CommentDto>
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
        $this->class = CommentDto::fromEntity(
            new Comment('posts', '42', 'Hello world', 'aaaaaaaa-0000-7000-8000-000000000001')
        );
        $this->reflection = new ReflectionClass(CommentDto::class);
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
     * Creates a Comment entity with deterministic test values.
     *
     * @return Comment A fully-populated domain entity.
     */
    protected function makeComment(): Comment
    {
        return new Comment('posts', '42', 'Hello world', 'aaaaaaaa-0000-7000-8000-000000000001');
    }
}
