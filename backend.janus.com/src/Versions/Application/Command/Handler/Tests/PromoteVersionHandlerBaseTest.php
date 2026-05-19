<?php

/**
 * @file PromoteVersionHandlerBaseTest.php
 *
 * Tests for PromoteVersionHandler construction.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\PromoteVersionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that PromoteVersionHandler can be constructed with its dependencies.
 */
#[CoversClass(className: PromoteVersionHandler::class)]
final class PromoteVersionHandlerBaseTest extends PromoteVersionHandlerTest
{
    public function testIsInstanceOfPromoteVersionHandler(): void
    {
        $this->assertInstanceOf(PromoteVersionHandler::class, $this->class);
    }
}
