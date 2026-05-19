<?php

/**
 * @file SchemaControllerBaseTest.php
 *
 * Basic structural tests for SchemaController.
 *
 * @package App\Schema\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\Controller\Tests;

use App\Schema\Presentation\Controller\SchemaController;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies SchemaController can be instantiated with valid dependencies.
 */
#[CoversClass(className: SchemaController::class)]
final class SchemaControllerBaseTest extends SchemaControllerTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(SchemaController::class, $this->class);
    }
}
