<?php

/**
 * @file GetActivityHandlerBaseTest.php
 *
 * Tests for GetActivityHandler construction and dependency wiring.
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\Query\Handler\GetActivityHandler;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetActivityHandler stores its repository dependency correctly after construction.
 */
#[CoversClass(className:  GetActivityHandler::class)]
final class GetActivityHandlerBaseTest extends GetActivityHandlerTest
{
    /**
     * Test that the handler stores the injected repository in its repository property.
     * @throws \ReflectionException
     */
    public function testConstructorStoresRepository(): void
    {
        $property = $this->reflection->getProperty(name: 'repository');
        $property->setAccessible(accessible: true);

        $this->assertSame(expected: $this->repository, actual: $property->getValue(object: $this->class));
    }

    /**
     * Test that GetActivityHandler can be instantiated with a mocked repository.
     */
    public function testIsInstantiable(): void
    {
        $this->assertInstanceOf(expected: GetActivityHandler::class, actual: $this->class);
    }

    /**
     * Test that the stored repository implements ActivityRepositoryInterface.
     * @throws \ReflectionException
     */
    public function testRepositoryIsActivityRepositoryInterface(): void
    {
        $property = $this->reflection->getProperty(name: 'repository');
        $property->setAccessible(accessible: true);

        $this->assertInstanceOf(expected: ActivityRepositoryInterface::class, actual: $property->getValue(object: $this->class));
    }
}
