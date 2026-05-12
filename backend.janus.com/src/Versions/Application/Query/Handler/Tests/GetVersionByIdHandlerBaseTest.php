<?php

/**
 * @file GetVersionByIdHandlerBaseTest.php
 *
 * Tests for GetVersionByIdHandler construction.
 *
 * @package App\Versions\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler\Tests;

use App\Versions\Application\Query\Handler\GetVersionByIdHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetVersionByIdHandler can be constructed with a repository.
 */
#[CoversClass(GetVersionByIdHandler::class)]
final class GetVersionByIdHandlerBaseTest extends GetVersionByIdHandlerTest
{
    public function testIsInstanceOfGetVersionByIdHandler(): void
    {
        $this->assertInstanceOf(GetVersionByIdHandler::class, $this->class);
    }
}
