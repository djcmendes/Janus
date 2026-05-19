<?php

/**
 * @file AssetsControllerBaseTest.php
 *
 * Constructor and dependency-wiring tests for AssetsController.
 *
 * @package App\Assets\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Presentation\Controller\Tests;

use App\Assets\Presentation\Controller\AssetsController;
use App\Heimdall\Application\Service\RequestGuard;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

#[CoversClass(className: AssetsController::class)]
final class AssetsControllerBaseTest extends AssetsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of AssetsController.
     */
    public function testIsInstanceOfAssetsController(): void
    {
        $this->assertInstanceOf(AssetsController::class, $this->class);
    }

    /**
     * Test that the guard property holds the injected RequestGuard instance.
     */
    public function testGuardIsWiredCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'guard'))->getValue($this->class);

        $this->assertInstanceOf(RequestGuard::class, $value);
        $this->assertSame($this->guard, $value);
    }
}
