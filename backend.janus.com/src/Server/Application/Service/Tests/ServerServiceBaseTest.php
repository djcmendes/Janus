<?php

/**
 * @file ServerServiceBaseTest.php
 *
 * Basic structural tests for ServerService.
 *
 * @package App\Server\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Application\Service\Tests;

use App\Server\Application\Service\ServerService;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that ServerService can be instantiated with valid arguments.
 */
#[CoversClass(className: ServerService::class)]
final class ServerServiceBaseTest extends ServerServiceTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(ServerService::class, $this->class);
    }
}
