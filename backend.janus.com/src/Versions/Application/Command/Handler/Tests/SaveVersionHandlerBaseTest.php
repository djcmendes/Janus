<?php

/**
 * @file SaveVersionHandlerBaseTest.php
 *
 * Tests for SaveVersionHandler construction.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\SaveVersionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that SaveVersionHandler can be constructed with a repository.
 */
#[CoversClass(SaveVersionHandler::class)]
final class SaveVersionHandlerBaseTest extends SaveVersionHandlerTest
{
    public function testIsInstanceOfSaveVersionHandler(): void
    {
        $this->assertInstanceOf(SaveVersionHandler::class, $this->class);
    }
}
