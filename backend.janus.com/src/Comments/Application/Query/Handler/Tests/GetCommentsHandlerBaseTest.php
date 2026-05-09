<?php

/**
 * @file GetCommentsHandlerBaseTest.php
 *
 * Constructor and interface compliance tests for GetCommentsHandler.
 *
 * @package App\Comments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler\Tests;

use App\Comments\Application\Query\Handler\GetCommentsHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that GetCommentsHandler stores its injected repository correctly.
 */
#[CoversClass(GetCommentsHandler::class)]
final class GetCommentsHandlerBaseTest extends GetCommentsHandlerTest
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
