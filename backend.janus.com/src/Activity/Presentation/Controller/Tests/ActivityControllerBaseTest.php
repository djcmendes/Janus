<?php

/**
 * @file ActivityControllerBaseTest.php
 *
 * Tests for ActivityController constructor and property initialisation.
 *
 * @package App\Activity\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Presentation\Controller\Tests;

use App\Activity\Application\Query\Handler\GetActivityByIdHandler;
use App\Activity\Application\Query\Handler\GetActivityHandler;
use App\Activity\Presentation\Controller\ActivityController;
use App\Heimdall\Domain\Service\RequestGuard;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that the ActivityController stores each injected dependency
 * in the correct private property after construction.
 */
#[CoversClass(ActivityController::class)]
final class ActivityControllerBaseTest extends ActivityControllerTest
{
    /**
     * Test that the guard property holds the injected RequestGuard instance.
     */
    public function testGuardIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'guard'))->getValue($this->class);

        $this->assertInstanceOf(RequestGuard::class, $value);
        $this->assertSame($this->guard, $value);
    }

    /**
     * Test that the getActivityHandler property holds the injected handler instance.
     */
    public function testGetActivityHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'getActivityHandler'))->getValue($this->class);

        $this->assertInstanceOf(GetActivityHandler::class, $value);
        $this->assertSame($this->getActivityHandler, $value);
    }

    /**
     * Test that the getActivityByIdHandler property holds the injected handler instance.
     */
    public function testGetActivityByIdHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'getActivityByIdHandler'))->getValue($this->class);

        $this->assertInstanceOf(GetActivityByIdHandler::class, $value);
        $this->assertSame($this->getActivityByIdHandler, $value);
    }
}
