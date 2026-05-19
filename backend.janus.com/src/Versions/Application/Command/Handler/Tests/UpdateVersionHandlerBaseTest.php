<?php

/**
 * @file UpdateVersionHandlerBaseTest.php
 *
 * Tests for UpdateVersionHandler construction.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\UpdateVersionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that UpdateVersionHandler can be constructed with a repository.
 */
#[CoversClass(className: UpdateVersionHandler::class)]
final class UpdateVersionHandlerBaseTest extends UpdateVersionHandlerTest
{
    public function testIsInstanceOfUpdateVersionHandler(): void
    {
        $this->assertInstanceOf(UpdateVersionHandler::class, $this->class);
    }
}
