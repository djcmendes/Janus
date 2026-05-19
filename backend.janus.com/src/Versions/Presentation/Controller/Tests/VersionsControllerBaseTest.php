<?php

/**
 * @file VersionsControllerBaseTest.php
 *
 * Tests that VersionsController can be instantiated.
 *
 * @package App\Versions\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\Controller\Tests;

use App\Versions\Presentation\Controller\VersionsController;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that the controller can be constructed and is the correct type.
 */
#[CoversClass(className: VersionsController::class)]
final class VersionsControllerBaseTest extends VersionsControllerTest
{
    public function testControllerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(VersionsController::class, $this->class);
    }
}
