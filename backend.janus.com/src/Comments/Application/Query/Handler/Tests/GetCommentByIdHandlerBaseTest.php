<?php

/**
 * @file GetCommentByIdHandlerBaseTest.php
 *
 * Constructor and interface compliance tests for GetCommentByIdHandler.
 *
 * @package App\Comments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler\Tests;

use App\Comments\Application\Query\Handler\GetCommentByIdHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that GetCommentByIdHandler stores its injected repository correctly.
 */
#[CoversClass(GetCommentByIdHandler::class)]
final class GetCommentByIdHandlerBaseTest extends GetCommentByIdHandlerTest
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
