<?php

/**
 * @file ApplySchemaHandlerBaseTest.php
 *
 * Basic structural tests for ApplySchemaHandler.
 *
 * @package App\Schema\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command\Handler\Tests;

use App\Schema\Application\Command\Handler\ApplySchemaHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies ApplySchemaHandler can be instantiated with all 13 dependencies.
 */
#[CoversClass(className: ApplySchemaHandler::class)]
final class ApplySchemaHandlerBaseTest extends ApplySchemaHandlerTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(ApplySchemaHandler::class, $this->class);
    }
}
