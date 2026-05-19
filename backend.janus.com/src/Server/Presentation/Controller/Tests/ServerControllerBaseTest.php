<?php

/**
 * @file ServerControllerBaseTest.php
 *
 * Basic structural tests for ServerController.
 *
 * @package App\Server\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Presentation\Controller\Tests;

use App\Server\Presentation\Controller\ServerController;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that ServerController can be instantiated with valid dependencies.
 */
#[CoversClass(className: ServerController::class)]
final class ServerControllerBaseTest extends ServerControllerTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(ServerController::class, $this->class);
    }
}
