<?php

/**
 * @file UpdateCommentHandlerTest.php
 *
 * Abstract base for all UpdateCommentHandler test suites.
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\UpdateCommentHandler;
use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for UpdateCommentHandler tests.
 *
 * Strategy: UpdateCommentHandler is final — instantiated as a real object
 * backed by a mocked CommentRepositoryInterface.
 */
#[CoversClass(className: UpdateCommentHandler::class)]
abstract class UpdateCommentHandlerTest extends TestCase
{
    /**
     * Mock of the comment repository dependency.
     * @var MockObject&CommentRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var UpdateCommentHandler
     */
    protected UpdateCommentHandler $class;

    /**
     * Reflection of UpdateCommentHandler.
     * @var ReflectionClass<UpdateCommentHandler>
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
        $this->repository = $this->createMock(CommentRepositoryInterface::class);
        $this->class      = new UpdateCommentHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(UpdateCommentHandler::class);
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
            $this->repository,
            $this->class,
            $this->reflection
        );
    }

    /**
     * Creates a Comment owned by the given userId for use in handle() tests.
     *
     * @param string $userId UUID of the comment author.
     *
     * @return Comment A hydrated domain entity with deterministic test values.
     */
    protected function makeComment(string $userId = 'owner-uuid'): Comment
    {
        return new Comment('posts', '42', 'Original text', $userId);
    }
}
