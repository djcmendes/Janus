<?php

/**
 * @file GetVersionsHandlerBaseTest.php
 *
 * Tests for GetVersionsHandler construction.
 *
 * @package App\Versions\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler\Tests;

use App\Versions\Application\Query\Handler\GetVersionsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetVersionsHandler can be constructed with a repository.
 */
#[CoversClass(GetVersionsHandler::class)]
final class GetVersionsHandlerBaseTest extends GetVersionsHandlerTest
{
    public function testIsInstanceOfGetVersionsHandler(): void
    {
        $this->assertInstanceOf(GetVersionsHandler::class, $this->class);
    }
}
