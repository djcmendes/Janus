<?php

/**
 * @file CreateCommentHandlerBaseTest.php
 *
 * Constructor and interface compliance tests for CreateCommentHandler.
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\CreateCommentHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that CreateCommentHandler stores its injected repository correctly.
 */
#[CoversClass(className: CreateCommentHandler::class)]
final class CreateCommentHandlerBaseTest extends CreateCommentHandlerTest
{
    /**
     * Test that the repository property holds the injected repository instance.
     */
    public function testRepositoryIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'repository'))->getValue($this->class);

        $this->assertSame($this->repository, $value);
    }
}
