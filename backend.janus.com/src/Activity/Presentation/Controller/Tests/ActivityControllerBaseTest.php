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
use ReflectionException;
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
     *
     * @throws ReflectionException
     */
    public function testGuardIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty(class: $this->class, property: 'guard'))->getValue(object: $this->class);

        $this->assertInstanceOf(expected: RequestGuard::class, actual: $value);
        $this->assertSame(expected: $this->guard, actual: $value);
    }

    /**
     * Test that the getActivityHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testGetActivityHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, property: 'getActivityHandler'))->getValue(object: $this->class);

        $this->assertInstanceOf(expected: GetActivityHandler::class, actual: $value);
        $this->assertSame(expected: $this->getActivityHandler, actual: $value);
    }

    /**
     * Test that the getActivityByIdHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testGetActivityByIdHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty(class: $this->class, property: 'getActivityByIdHandler'))->getValue(object: $this->class);

        $this->assertInstanceOf(expected: GetActivityByIdHandler::class, actual: $value);
        $this->assertSame(expected: $this->getActivityByIdHandler, actual: $value);
    }
}
