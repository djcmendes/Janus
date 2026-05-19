<?php

/**
 * @file GetCommentsHandlerTest.php
 *
 * Abstract base for all GetCommentsHandler test suites.
 *
 * @package App\Comments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler\Tests;

use App\Comments\Application\Query\Handler\GetCommentsHandler;
use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetCommentsHandler tests.
 *
 * Strategy: GetCommentsHandler is final — instantiated as a real object
 * backed by a mocked CommentRepositoryInterface.
 */
#[CoversClass(className: GetCommentsHandler::class)]
abstract class GetCommentsHandlerTest extends TestCase
{
    /**
     * Mock of the comment repository dependency.
     * @var MockObject&CommentRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var GetCommentsHandler
     */
    protected GetCommentsHandler $class;

    /**
     * Reflection of GetCommentsHandler.
     * @var ReflectionClass<GetCommentsHandler>
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
        $this->class      = new GetCommentsHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(GetCommentsHandler::class);
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
     * Creates a Comment entity with deterministic test values.
     *
     * @return Comment A hydrated domain entity.
     */
    protected function makeComment(): Comment
    {
        return new Comment('posts', '42', 'Hello world', 'aaaaaaaa-0000-7000-8000-000000000001');
    }
}
