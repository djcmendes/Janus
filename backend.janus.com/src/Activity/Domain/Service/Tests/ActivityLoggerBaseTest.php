<?php

/**
 * @file ActivityLoggerBaseTest.php
 *
 * Tests for ActivityLogger construction and dependency wiring.
 *
 * @package App\Activity\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Service\Tests;

use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Activity\Domain\Service\ActivityLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Verifies that ActivityLogger stores its dependencies correctly after construction.
 */
#[CoversClass(ActivityLogger::class)]
final class ActivityLoggerBaseTest extends ActivityLoggerTest
{
    /**
     * Test that ActivityLogger can be instantiated with its mocked dependencies.
     */
    public function testIsInstantiable(): void
    {
        $this->assertInstanceOf(ActivityLogger::class, $this->class);
    }

    /**
     * Test that the constructor stores the injected repository in its repository property.
     */
    public function testConstructorStoresRepository(): void
    {
        $property = $this->reflection->getProperty('repository');
        $property->setAccessible(true);

        $this->assertInstanceOf(ActivityRepositoryInterface::class, $property->getValue($this->class));
        $this->assertSame($this->repository, $property->getValue($this->class));
    }

    /**
     * Test that the constructor stores the injected request stack in its requestStack property.
     */
    public function testConstructorStoresRequestStack(): void
    {
        $property = $this->reflection->getProperty('requestStack');
        $property->setAccessible(true);

        $this->assertInstanceOf(RequestStack::class, $property->getValue($this->class));
        $this->assertSame($this->requestStack, $property->getValue($this->class));
    }
}
