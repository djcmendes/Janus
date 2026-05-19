<?php

/**
 * @file UpdateCommentHandlerBaseTest.php
 *
 * Constructor and interface compliance tests for UpdateCommentHandler.
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\UpdateCommentHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that UpdateCommentHandler stores its injected repository correctly.
 */
#[CoversClass(className: UpdateCommentHandler::class)]
final class UpdateCommentHandlerBaseTest extends UpdateCommentHandlerTest
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
