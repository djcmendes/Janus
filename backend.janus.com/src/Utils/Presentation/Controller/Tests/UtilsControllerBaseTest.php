<?php

/**
 * @file UtilsControllerBaseTest.php
 *
 * Basic structural tests for UtilsController.
 *
 * @package App\Utils\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Utils\Presentation\Controller\Tests;

use App\Utils\Presentation\Controller\UtilsController;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that UtilsController can be instantiated with valid dependencies.
 */
#[CoversClass(className: UtilsController::class)]
final class UtilsControllerBaseTest extends UtilsControllerTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(UtilsController::class, $this->class);
    }
}
