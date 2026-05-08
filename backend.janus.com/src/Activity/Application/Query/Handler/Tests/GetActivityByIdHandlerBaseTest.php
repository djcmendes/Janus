<?php

/**
 * @file GetActivityByIdHandlerBaseTest.php
 *
 * Tests for GetActivityByIdHandler construction and dependency wiring.
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\Query\Handler\GetActivityByIdHandler;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetActivityByIdHandler stores its repository dependency correctly after construction.
 */
#[CoversClass(GetActivityByIdHandler::class)]
final class GetActivityByIdHandlerBaseTest extends GetActivityByIdHandlerTest
{
    /**
     * Test that the handler stores the injected repository in its repository property.
     */
    public function testConstructorStoresRepository(): void
    {
        $property = $this->reflection->getProperty(name: 'repository');
        $property->setAccessible(accessible: true);

        $this->assertSame(expected: $this->repository, actual: $property->getValue(object: $this->class));
    }

    /**
     * Test that GetActivityByIdHandler can be instantiated with a mocked repository.
     */
    public function testImplementsNoInterfaceButIsInstantiable(): void
    {
        $this->assertInstanceOf(expected: GetActivityByIdHandler::class, actual: $this->class);
    }

    /**
     * Test that the stored repository implements ActivityRepositoryInterface.
     */
    public function testRepositoryIsActivityRepositoryInterface(): void
    {
        $property = $this->reflection->getProperty(name: 'repository');
        $property->setAccessible(accessible: true);

        $this->assertInstanceOf(
            expected: ActivityRepositoryInterface::class,
            actual: $property->getValue(object: $this->class)
        );
    }
}
