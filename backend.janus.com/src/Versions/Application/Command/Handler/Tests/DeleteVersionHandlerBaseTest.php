<?php

/**
 * @file DeleteVersionHandlerBaseTest.php
 *
 * Tests for DeleteVersionHandler construction.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\DeleteVersionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that DeleteVersionHandler can be constructed with a repository.
 */
#[CoversClass(DeleteVersionHandler::class)]
final class DeleteVersionHandlerBaseTest extends DeleteVersionHandlerTest
{
    public function testIsInstanceOfDeleteVersionHandler(): void
    {
        $this->assertInstanceOf(DeleteVersionHandler::class, $this->class);
    }
}
