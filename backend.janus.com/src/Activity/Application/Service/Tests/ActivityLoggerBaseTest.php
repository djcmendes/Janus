<?php

/**
 * @file ActivityLoggerBaseTest.php
 *
 * Tests for ActivityLogger construction and dependency wiring.
 *
 * @package App\Activity\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Service\Tests;

use App\Activity\Application\Service\ActivityLogger;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Verifies that ActivityLogger stores its dependencies correctly after construction.
 */
#[CoversClass(className:  ActivityLogger::class)]
final class ActivityLoggerBaseTest extends ActivityLoggerTest
{
    /**
     * Test that ActivityLogger can be instantiated with its mocked dependencies.
     */
    public function testIsInstantiable(): void
    {
        $this->assertInstanceOf(expected: ActivityLogger::class, actual: $this->class);
    }

    /**
     * Test that the constructor stores the injected repository in its repository property.
     */
    public function testConstructorStoresRepository(): void
    {
        $property = $this->reflection->getProperty(name: 'repository');
        $property->setAccessible(accessible: true);

        $this->assertInstanceOf(expected: ActivityRepositoryInterface::class, actual: $property->getValue(object: $this->class));
        $this->assertSame(expected: $this->repository, actual: $property->getValue(object: $this->class));
    }

    /**
     * Test that the constructor stores the injected request stack in its requestStack property.
     */
    public function testConstructorStoresRequestStack(): void
    {
        $property = $this->reflection->getProperty(name: 'requestStack');
        $property->setAccessible(accessible: true);

        $this->assertInstanceOf(expected: RequestStack::class, actual: $property->getValue(object: $this->class));
        $this->assertSame(expected: $this->requestStack, actual: $property->getValue(object: $this->class));
    }
}
