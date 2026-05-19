<?php

/**
 * @file DeleteCommentHandlerBaseTest.php
 *
 * Constructor and interface compliance tests for DeleteCommentHandler.
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\DeleteCommentHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that DeleteCommentHandler stores its injected repository correctly.
 */
#[CoversClass(className: DeleteCommentHandler::class)]
final class DeleteCommentHandlerBaseTest extends DeleteCommentHandlerTest
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
