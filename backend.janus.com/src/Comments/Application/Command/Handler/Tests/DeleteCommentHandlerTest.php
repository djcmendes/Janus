<?php

/**
 * @file DeleteCommentHandlerTest.php
 *
 * Abstract base for all DeleteCommentHandler test suites.
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\DeleteCommentHandler;
use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for DeleteCommentHandler tests.
 *
 * Strategy: DeleteCommentHandler is final — instantiated as a real object
 * backed by a mocked CommentRepositoryInterface.
 */
#[CoversClass(DeleteCommentHandler::class)]
abstract class DeleteCommentHandlerTest extends TestCase
{
    /**
     * Mock of the comment repository dependency.
     * @var MockObject&CommentRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var DeleteCommentHandler
     */
    protected DeleteCommentHandler $class;

    /**
     * Reflection of DeleteCommentHandler.
     * @var ReflectionClass<DeleteCommentHandler>
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
        $this->class      = new DeleteCommentHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(DeleteCommentHandler::class);
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
        return new Comment('posts', '42', 'Hello world', $userId);
    }
}
