<?php

/**
 * @file CreateCommentHandlerTest.php
 *
 * Abstract base for all CreateCommentHandler test suites.
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\CreateCommentHandler;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for CreateCommentHandler tests.
 *
 * Strategy: CreateCommentHandler is final — instantiated as a real object
 * backed by a mocked CommentRepositoryInterface.
 */
#[CoversClass(className: CreateCommentHandler::class)]
abstract class CreateCommentHandlerTest extends TestCase
{
    /**
     * Mock of the comment repository dependency.
     * @var MockObject&CommentRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var CreateCommentHandler
     */
    protected CreateCommentHandler $class;

    /**
     * Reflection of CreateCommentHandler.
     * @var ReflectionClass<CreateCommentHandler>
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
        $this->class      = new CreateCommentHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(CreateCommentHandler::class);
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
}
